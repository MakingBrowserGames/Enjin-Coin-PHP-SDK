<?php
namespace EnjinCoin;

use EnjinCoin\Notifications\PusherAdapter;

class Notifications {
	private static $adapter = null;

	const TYPE_EVENT = 'Event';
	const CHANNEL_GAME_SERVER = 'game_server';
	const CHANNEL_GAME_CLIENT = 'game_client';
	const CHANNEL_WALLET = 'wallet';

	public static $channels = [
		'game_server',
		'game_client',
		'wallet',
	];

	public static $notification_types = [
		self::TYPE_EVENT
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
}