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

/**
 * Class TransactionRequests
 * @package EnjinCoin\Api
 */
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

	public static $txrTypes = [
		self::TYPE_BUY,
		self::TYPE_SELL,
		self::TYPE_SEND,
		self::TYPE_USE,
		//self::TYPE_SUBSCRIBE // not implemented yet
	];

	/**
	 * Function to get a transaction request
	 * @param int $txrId
	 * @return mixed
	 */
	public function get(int $txrId) {
		$select = $this->db->select()
			->from('transaction_requests')
			->where(['txr_id' => $txrId]);

		$results = Db::query($select);
		return $results->current();
	}

	/**
	 * Get the latest transaction request - used by tests
	 * @param int $limit
	 * @return mixed
	 */
	public function getLatest($limit = 1) {
		$select = $this->db->select()
			->from('transaction_requests')
			->order('txr_id desc')
			->limit($limit);

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
	 * @param int $tokenId
	 * @param string $value
	 * @throws Exception if identities dont exist or the transaction request value is invalid
	 * @return bool
	 */
	public function create(array $identity, array $recipient, string $type, string $icon = null, string $title = null, int $tokenId = 0, string $value = '0') {
		// Validate Txr Type
		if (!in_array($type, self::$txrTypes)) {
			throw new Exception('Invalid Transaction Request Type');
		}

		// Validate Identity
		$identities = new Identities();
		$ident = $identities->get($identity);

		if (!empty($ident)) {
			$ident = reset($ident);
		}
		if (empty($ident['identity_id'])) {
			throw new Exception('Identity does not exist');
		}

		// Validate Recipient
		$recipientDb = ['recipient_id' => null, 'ethereum_address' => null];
		$recip = $identities->get($recipient);

		if (!empty($recip)) {
			$recip = reset($recip);
			$recipientDb['recipient_id'] = $recip['identity_id'];
		}

		if (!empty($recip['ethereum_address']) && Ethereum::validateAddress($recip['ethereum_address'])) {
			$recipientDb['recipient_address'] = $recip['ethereum_address'];
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
			'recipient_id' => $recipientDb['recipient_id'],
			'recipient_address' => $recipientDb['recipient_address'],
			'icon' => $icon,
			'title' => $title,
			'token_id' => $tokenId,
			'value' => $value,
			'state' => self::STATE_PENDING
		], $insert::VALUES_MERGE);

		$results = Db::query($insert);
		$txrId = $results->getGeneratedValue();

		// Create event
		(new Events)->create(Auth::appId(), EventTypes::TXR_PENDING, [
			'txr_id' => $txrId,
			'identity' => $identity,
			'recipient' => $recipient,
			'type' => $type,
			'icon' => $icon,
			'title' => $title,
			'value' => $value,
		]);

		return $txrId;
	}

	/**
	 * Cancel a Transaction Request
	 * @param int $txrId
	 * @throws Exception if no permission to cancel the transaction request
	 * @return bool
	 */
	public function cancel(int $txrId) {
		$txr = $this->get($txrId);

		// Check permissions for cancellation type
		$eventType = null;
		if (Auth::role() <= Auth::ROLE_APP) {
			$eventType = EventTypes::TXR_CANCELED_PLATFORM;
		} else if (Auth::role() <= Auth::ROLE_WALLET) {
			$eventType = EventTypes::TXR_CANCELED_USER;
		} else {
			throw new Exception('Authentication required');
		}

		$identity = Auth::identity();
		$identityId = $identity['identity_id'];

		if (empty($eventType)
			|| ($eventType === EventTypes::TXR_CANCELED_USER && empty($identityId))
			|| ($eventType === EventTypes::TXR_CANCELED_USER && $txr['identity_id'] !== $identityId)) {
			throw new Exception('You do not have permission to cancel this transaction request');
		}

		// Update the new transaction request state
		$sql = $this->db->update('transaction_requests')
			->where(['txr_id' => $txrId])
			->set(['state' => $eventType === EventTypes::TXR_CANCELED_USER ? self::STATE_CANCELED_USER : self::STATE_CANCELED_PLATFORM]);
		Db::query($sql);

		(new Events)->create(Auth::appId(), $eventType, ['txr_id' => $txrId, 'identity' => $identity]);

		return true;
	}

	/**
	 * Function to broadcast a transaction request
	 * @param int $txrId
	 * @param array $data
	 * @return bool|mixed
	 */
	public function broadcast(int $txrId, array $data) {
		$txr = $this->get($txrId);

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
			$txId = $model->msg('eth_sendRawTransaction', array($data));

			// Update the new transaction request state
			$sql = $this->db->update('transaction_requests')
				->where(['txr_id' => $txrId])
				->set(['state' => self::STATE_BROADCASTED]);
			Db::query($sql);

			(new Events)->create(Auth::appId(), EventTypes::TX_BROADCASTED, ['txr_id' => $txrId, 'tx_id' => $txId]);

			return $txId;
		}
		return false;
	}
}
