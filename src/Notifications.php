<?php
namespace EnjinCoin;

use EnjinCoin\Notifications\PusherAdapter;

class Notifications {
	private static $adapter = null;

	const TYPE_EVENT = 'Event';
	const TYPE_PRICE = 'Price';

	public const CHANNEL_WALLETS = 'wallets'; // global wallet channel
	private const CHANNEL_SDK_SERVER = 'server';
	private const CHANNEL_SDK_CLIENT = 'client';
	private const CHANNEL_WALLET = 'wallet';

	public static $channels = [
		self::CHANNEL_SDK_SERVER,
		self::CHANNEL_SDK_CLIENT,
		self::CHANNEL_WALLETS,
		self::CHANNEL_WALLET,
	];

	public static $notification_types = [
		self::TYPE_EVENT,
		self::TYPE_PRICE,
	];

	private static function getAdapter() {
		if (empty(self::$adapter)) {
			switch (Config::get()->notifications->method) {
				case 'pusher':
					self::$adapter = new PusherAdapter();
					break;
			}
		}

		return self::$adapter;
	}

	public static function notify($channel, $event, $data) {
		self::getAdapter()->notify($channel, $event, $data);
	}

	public static function getWalletChannel(string $auth_key) {
		return hash('sha512', password_hash($auth_key . self::CHANNEL_WALLET, PASSWORD_BCRYPT));
	}

	public static function getSdkServerChannel(string $auth_key) {
		return hash('sha512', password_hash($auth_key . self::CHANNEL_SDK_SERVER, PASSWORD_BCRYPT));
	}

	public static function getSdkClientChannel(string $auth_key) {
		return hash('sha512', password_hash($auth_key . self::CHANNEL_SDK_CLIENT, PASSWORD_BCRYPT));
	}

	public static function getClientInfo() {
		return self::getAdapter()->getClientInfo();
	}
}
