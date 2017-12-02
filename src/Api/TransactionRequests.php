<?php
namespace EnjinCoin\Api;

use EnjinCoin\Auth;
use EnjinCoin\Ethereum;
use EnjinCoin\EventTypes;
use PHPUnit\Runner\Exception;
use Zend;
use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;

class TransactionRequests extends ApiBase {
	const TYPE_BUY = 'buy';
	const TYPE_SELL = 'sell';
	const TYPE_SEND = 'send';
	const TYPE_USE = 'use';
	//const TYPE_SUBSCRIBE = 'subscribe';

	public static $txr_types = [
		self::TYPE_BUY,
		self::TYPE_SELL,
		self::TYPE_SEND,
		self::TYPE_USE,
		//self::TYPE_SUBSCRIBE // not implemented yet
	];

	public function get(int $txr_id) {
		$select = $this->db->select()
			->from('transaction_requests');
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
		if(!in_array($type, self::$txr_types)) throw new \Exception('Invalid Transaction Request Type');

		// Validate Identity
		$identities = new Identities();
		$ident = $identities->get($identity);
		$ident = reset($ident);
		if (!empty($ident)) $ident = reset($ident);
		if (empty($ident['identity_id'])) throw new Exception('Identity does not exist');

		// Validate Recipient
		$recipient_db = ['recipient_id' => null, 'ethereum_address' => null];
		$recip = $identities->get($recipient);
		$recip = reset($recip);
		if (!empty($recip)) {
			$recip = reset($recip);
			$recipient_db['recipient_id'] = $recip['identity_id'];
		}
		if (!empty($recip['ethereum_address']) && Ethereum::validateAddress($recip['ethereum_address'])) {
			$recipient_db['recipient_address'] = $recip['ethereum_address'];
		}
		else throw new Exception('Identity does not exist');

		// Validate Value
		if(!Ethereum::validateValue($value)) throw new Exception('Invalid Transaction Request Value');

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
			'title' => $icon,
			'token_id' => $token_id,
			'value' => $value,
		], $insert::VALUES_MERGE);

		// Create event and notification @todo

		return true;
	}

	/**
	 * Cancel a Transaction Request
	 * @param $txr_id
	 * @return bool
	 */
	public function cancel($txr_id) {
		$txr = $this->get($txr_id);

		// Check permissions for cancellation type @todo

		$event_type = EventTypes::TXR_CANCELED_USER;
		$event_type = EventTypes::TXR_CANCELED_PLATFORM;

		$delete = $this->db->delete('transaction_requests');
		$delete->where(['txr_id' => $txr_id]);
		Db::query($delete);

		(new Events)->create(Auth::appId(), $event_type, ['txr_id' => $txr_id]);

		return true;
	}
}
