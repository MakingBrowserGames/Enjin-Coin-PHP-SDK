<?php
namespace EnjinCoin\Api;

use Zend;
use EnjinCoin\Auth;
use EnjinCoin\Ethereum;
use EnjinCoin\EventTypes;
use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;
use EnjinCoin\Ethereum as Eth;
use PHPUnit\Runner\Exception;

class TransactionRequests extends ApiBase {
	const TYPE_BUY = 'buy';
	const TYPE_SELL = 'sell';
	const TYPE_SEND = 'send';
	const TYPE_USE = 'use';
	//const TYPE_SUBSCRIBE = 'subscribe';

	const STATE_PENDING = 'pending';
	const STATE_BROADCASTED = 'broadcasted';
	const STATE_EXECUTED = 'executed';
	const STATE_CONFIRMED = 'confirmed';
	const STATE_CANCELED_USER = 'canceled_user';
	const STATE_CANCELED_PLATFORM = 'canceled_platform';

	public static $txr_types = [
		self::TYPE_BUY,
		self::TYPE_SELL,
		self::TYPE_SEND,
		self::TYPE_USE,
		//self::TYPE_SUBSCRIBE // not implemented yet
	];

	public function get(int $txr_id) {
		$select = $this->db->select()
			->from('transaction_requests')
			->where(['txr_id' => $txr_id]);

		$results = Db::query($select);
		return $results->current();
	}

	/**
	 * Create a new Transaction Request
	 * @param array $identity
	 * @param array $recipient
	 * @param string $type
	 * @param string|null $icon
	 * @param string|null $title
	 * @param int $token_id
	 * @param string $value
	 * @return bool
	 * @throws \Exception
	 */
	public function create(array $identity, array $recipient, string $type, string $icon = null, string $title = null, int $token_id = 0, string $value = '0') {
		// Validate Txr Type
		if (!in_array($type, self::$txr_types)) {
			throw new Exception('Invalid Transaction Request Type');
		}

		// Validate Identity
		$identities = new Identities();
		$ident = $identities->get($identity);
		//@Damien - do we need to perfrom reste here and if !empty?? - will only work when I comment out the first rest($ident)
		//$ident = reset($ident);
		if (!empty($ident)) { 
			$ident = reset($ident);
		}
		if (empty($ident['identity_id'])) {
			throw new Exception('Identity does not exist');
		}

		// Validate Recipient
		$recipient_db = ['recipient_id' => null, 'ethereum_address' => null];
		$recip = $identities->get($recipient);
		//@Damien - do we need to perfrom reste here and if !empty?? - will only work when I comment out the first rest($recip)
		//$recip = reset($recip);

		if (!empty($recip)) {
			$recip = reset($recip);
			$recipient_db['recipient_id'] = $recip['identity_id'];
		}
		
		if (!empty($recip['ethereum_address']) && Ethereum::validateAddress($recip['ethereum_address'])) {
			$recipient_db['recipient_address'] = $recip['ethereum_address'];
		} else { 
			throw new Exception('Recipient Identity does not exist');
		}

		// Validate Value
		if (!Ethereum::validateValue($value)) { 
			throw new Exception('Invalid Transaction Request Value');
		}
		
		$insert = $this->db->insert('transaction_requests');
		$timestamp = time();
		$insert->values([
			'timestamp' => $timestamp,
			'app_id' => Auth::appId(),
			'identity_id' => !empty($ident) ? $ident['identity_id'] : 0,
			'type' => $type,
			'recipient_id' => $recipient_db['recipient_id'],
			'recipient_address' => $recipient_db['recipient_address'],
			'icon' => $icon,
			'title' => $title,
			'token_id' => $token_id,
			'value' => $value,
			'state' => self::STATE_PENDING
		], $insert::VALUES_MERGE);

		$results = Db::query($insert);
		$txr_id = $results->getGeneratedValue();

		// Create event
		(new Events)->create(Auth::appId(), EventTypes::TXR_PENDING, [
			'txr_id' => $txr_id,
			'identity' => $identity,
			'recipient' => $recipient,
			'type' => $type,
			'icon' => $icon,
			'title' => $title,
			'value' => $value,
		]);

		return true;
	}

	/**
	 * Cancel a Transaction Request
	 * @param int $txr_id
	 * @return bool
	 */
	public function cancel(int $txr_id) {
		$txr = $this->get($txr_id);

		// Check permissions for cancellation type
		$event_type = null;
		if (Auth::role() <= Auth::ROLE_APP)
			$event_type = EventTypes::TXR_CANCELED_PLATFORM;
		else if (Auth::role() <= Auth::ROLE_WALLET)
			$event_type = EventTypes::TXR_CANCELED_USER;
		else
			throw new Exception('Authentication required');

		$identity = Auth::identity();
		$identity_id = $identity['identity_id'];

		if (empty($event_type)
			|| ($event_type == EventTypes::TXR_CANCELED_USER && empty($identity_id))
			|| ($event_type == EventTypes::TXR_CANCELED_USER && $txr['identity_id'] != $identity_id))
			throw new Exception('You do not have permission to cancel this transaction request');

		// Update the new transaction request state
		$sql = $this->db->update('transaction_requests')
			->where(['txr_id' => $txr_id])
			->set(['state' => $event_type == EventTypes::TXR_CANCELED_USER ? self::STATE_CANCELED_USER : self::STATE_CANCELED_PLATFORM]);
		Db::query($sql);

		(new Events)->create(Auth::appId(), $event_type, ['txr_id' => $txr_id]);

		return true;
	}

	public function broadcast(int $txr_id, array $data) {
		$txr = $this->get($txr_id);

		if (!empty($txr)) {
			// @todo: RLP decoding and validation of all transaction data
			/*
			// Validate address
			if (!Eth::validateAddress($transaction['from']) || !Eth::validateAddress($transaction['to'])) {
				throw new Exception('Invalid address');
			}

			// Validate Transaction Request with the Transaction
			if($transaction['from'] != $txr['from'])
				throw new Exception('Invalid transaction from');
			if($transaction['to'] != $txr['to'])
				throw new Exception('Invalid transaction to');
			if(Numbers::decodeHex($transaction['value']) != $txr['value'])
				throw new Exception('Invalid transaction value');
			*/

			$model = new Eth;
			$tx_id = $model->msg('eth_sendRawTransaction', array($data));

			// Update the new transaction request state
			$sql = $this->db->update('transaction_requests')
				->where(['txr_id' => $txr_id])
				->set(['state' => self::STATE_BROADCASTED]);
			Db::query($sql);

			(new Events)->create(Auth::appId(), EventTypes::TX_BROADCASTED, ['txr_id' => $txr_id, 'tx_id' => $tx_id]);

			return $tx_id;
		}
		return false;
	}
}
