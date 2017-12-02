<?php
namespace EnjinCoin;

use EnjinCoin\Notifications\PusherAdapter;

class Notifications {
	private static $adapter = null;

	const TYPE_EVENT = 'Event';
	const TYPE_PRICE = 'Price';

	const CHANNEL_GAME_SERVER = 'game_server';
	const CHANNEL_GAME_CLIENT = 'game_client';
	const CHANNEL_WALLETS = 'wallets'; // global wallet channel
	const CHANNEL_WALLET = 'wallet';

	public static $channels = [
		self::CHANNEL_GAME_SERVER,
		self::CHANNEL_GAME_CLIENT,
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

	public static function getWalletChannel($ethereum_address) {
		return self::CHANNEL_WALLET . '_' . $ethereum_address;
	}
}
