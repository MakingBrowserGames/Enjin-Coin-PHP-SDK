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

	public function callEvent($event_type, $app_id, $identity_id, $arguments) {
		if (in_array($event_type, self::$event_types) && method_exists($this, $event_type)) {
			$data = call_user_func_array(array($this, $event_type), $arguments);
			return $this->createEvent($event_type, $app_id, $identity_id, $data);
		}

		return false;
	}

	public function txr_pending($txr_id, $identity, $recipient, $type, $icon, $title, $value) {
		$identities = new Identities();

		return [
			'txr_id' => $txr_id,
			'identity' => $identities->get($identity),
			'recipient' => $identities->get($recipient),
			'type' => $type,
			'icon' => $icon,
			'title' => $title,
			'value' => $value,
			'state' => 'pending',
		];
	}

	public function txr_canceled_user($txr_id, $identity, $recipient, $type, $icon, $title, $value) {
		$identities = new Identities();

		return [
			'txr_id' => $txr_id,
			'identity' => $identities->get($identity),
			'recipient' => $identities->get($recipient),
			'type' => $type,
			'icon' => $icon,
			'title' => $title,
			'value' => $value,
			'state' => 'canceled_user',
		];
	}

	public function txr_canceled_platform($txr_id, $identity, $recipient, $type, $icon, $title, $value) {
		$identities = new Identities();

		return [
			'txr_id' => $txr_id,
			'identity' => $identities->get($identity),
			'recipient' => $identities->get($recipient),
			'type' => $type,
			'icon' => $icon,
			'title' => $title,
			'value' => $value,
			'state' => 'canceled_user',
		];
	}

	public function txr_accepted($txr_id, $identity, $recipient, $type, $icon, $title, $value) {
		$identities = new Identities();

		return [
			'txr_id' => $txr_id,
			'identity' => $identities->get($identity),
			'recipient' => $identities->get($recipient),
			'type' => $type,
			'icon' => $icon,
			'title' => $title,
			'value' => $value,
			'state' => 'accepted',
		];
	}

	public function identity_created($identity) {
		$identities = new Identities();
		return ['identity' => $identities->get($identity)];
	}

	public function identity_linked($identity) {
		$identities = new Identities();
		return ['identity' => $identities->get($identity)];
	}

	public function identity_updated($identity) {
		$identities = new Identities();
		return ['identity' => $identities->get($identity)];
	}

	public function identity_deleted($identity) {
		return ['identity' => $identity];
	}

	public function balance_updated($identity, $from, $pending, $confirmed) {
		$identities = new Identities();

		return [
			'identity' => $identities->get($identity),
			'from' => $identities->get($from),
			'pending' => $pending,
			'confirmed' => $confirmed,
		];
	}

	public function balance_melted($identity, $from, $pending, $confirmed, $enj) {
		$identities = new Identities();

		return [
			'identity' => $identities->get($identity),
			'from' => $identities->get($from),
			'pending' => $pending,
			'confirmed' => $confirmed,
			'ENJ' => $enj
		];
	}

	public function token_updated($token_id) {
		$tokens = new Tokens();
		return $tokens->get($token_id);
	}

	public function token_created($token_id) {
		$tokens = new Tokens();
		return $tokens->get($token_id);
	}

	private function createEvent($app_id, $identity_id, $event_type, $data) {
		return [
			'app_id' => $app_id,
			'identity_id' => $identity_id,
			'event_type' => $event_type,
			'data' => json_encode($data)
		];
	}
}
