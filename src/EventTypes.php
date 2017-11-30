<?php
namespace EnjinCoin;

use EnjinCoin\Api\Identities;
use EnjinCoin\Api\Tokens;

class EventTypes {
	const UNKNOWN_EVENT = 0;
	const TXR_PENDING = 1;
	const TXR_CANCELED_USER = 2;
	const TXR_CANCELED_PLATFORM = 3;
	const TXR_ACCEPTED = 4;
	const TX_BROADCASTED = 5;
	const TX_EXECUTED = 6;
	const TX_CONFIRMED = 7;
	const IDENTITY_CREATED = 8;
	const IDENTITY_LINKED = 9;
	const IDENTITY_UPDATED = 10;
	const IDENTITY_DELETED = 11;
	const BALANCE_UPDATED = 12;
	const BALANCE_MELTED = 13;
	const TOKEN_UPDATED = 14;
	const TOKEN_CREATED = 15;

	public static $event_types = [
		self::UNKNOWN_EVENT => 'unknown',
		self::TXR_PENDING => 'txr_pending',
		self::TXR_CANCELED_USER => 'txr_canceled_user',
		self::TXR_CANCELED_PLATFORM => 'txr_canceled_platform',
		self::TXR_ACCEPTED => 'txr_accepted',
		self::TX_BROADCASTED => 'tx_broadcasted',
		self::TX_EXECUTED => 'tx_executed',
		self::TX_CONFIRMED => 'tx_confirmed',
		self::IDENTITY_CREATED => 'identity_created',
		self::IDENTITY_LINKED => 'identity_linked',
		self::IDENTITY_UPDATED => 'identity_updated',
		self::IDENTITY_DELETED => 'identity_deleted',
		self::BALANCE_UPDATED => 'balance_updated',
		self::BALANCE_MELTED => 'balance_melted',
		self::TOKEN_UPDATED => 'token_updated',
		self::TOKEN_CREATED => 'token_created',
	];

	public function callEvent($event_type, $app_id, $identity_id, $params) {
		$event_string = self::$event_types[$event_type];

	    if (in_array($event_string, self::$event_types) && method_exists($this, $event_string)) {
			$data = $this->$event_string($params);
			return $this->createEvent([
				'event_type' => $event_type,
				'app_id' => $app_id,
				'identity_id' => $identity_id,
				'data' => $data
			]);
		}

		return false;
	}

	public function txr_pending($params) {
		$identities = new Identities();

		return [
			'txr_id' => $params['txr_id'],
			'identity' => $identities->get($params['identity']),
			'recipient' => $identities->get($params['recipient']),
			'type' => $params['type'],
			'icon' => $params['icon'],
			'title' => $params['title'],
			'value' => $params['value'],
			'state' => 'pending',
		];
	}

	public function txr_canceled_user($params) {
		$identities = new Identities();

		return [
			'txr_id' => $params['txr_id'],
			'identity' => $identities->get($params['identity']),
			'recipient' => $identities->get($params['recipient']),
			'type' => $params['type'],
			'icon' => $params['icon'],
			'title' => $params['title'],
			'value' => $params['value'],
			'state' => 'canceled_user',
		];
	}

	public function txr_canceled_platform($params) {
		$identities = new Identities();

		return [
			'txr_id' => $params['txr_id'],
			'identity' => $identities->get($params['identity']),
			'recipient' => $identities->get($params['recipient']),
			'type' => $params['type'],
			'icon' => $params['icon'],
			'title' => $params['title'],
			'value' => $params['value'],
			'state' => 'canceled_user',
		];
	}

	public function txr_accepted($params) {
		$identities = new Identities();

		return [
			'txr_id' => $params['txr_id'],
			'identity' => $identities->get($params['identity']),
			'recipient' => $identities->get($params['recipient']),
			'type' => $params['type'],
			'icon' => $params['icon'],
			'title' => $params['title'],
			'value' => $params['value'],
			'state' => 'accepted',
		];
	}

	public function identity_created($params) {
		$identities = new Identities();
		return ['identity' => $identities->get($params['identity']), 'linking_code' => $params['linking_code']];
	}

	public function identity_linked($params) {
		$identities = new Identities();
		return ['identity' => $identities->get($params['identity'])];
	}

	public function identity_updated($params) {
		$identities = new Identities();
		return ['identity' => $identities->get($params['identity'])];
	}

	public function identity_deleted($params) {
		return ['identity' => $params['identity']];
	}

	public function balance_updated($params) {
		$identities = new Identities();

		return [
			'identity' => $identities->get($params['identity']),
			'from' => $identities->get($params['from']),
			'pending' => $params['pending'],
			'confirmed' => $params['confirmed'],
		];
	}

	public function balance_melted($params) {
		$identities = new Identities();

		return [
			'identity' => $identities->get($params['identity']),
			'from' => $identities->get($params['from']),
			'pending' => $params['pending'],
			'confirmed' => $params['confirmed'],
			'ENJ' => $params['enj']
		];
	}

	public function token_updated($params) {
		$tokens = new Tokens();
		return $tokens->get($params['token_id']);
	}

	public function token_created($params) {
		$tokens = new Tokens();
		return $tokens->get($params['token_id']);
	}

	private function createEvent($params) {
		return [
			'app_id' => $params['app_id'],
			'identity_id' => $params['identity_id'],
			'event_type' => $params['event_type'],
			'data' => json_encode($params['data'])
		];
	}
}
