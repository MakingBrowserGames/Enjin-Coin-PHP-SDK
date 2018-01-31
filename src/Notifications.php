<?php
namespace EnjinCoin;

use EnjinCoin\Notifications\PusherAdapter;
use PHPUnit\Runner\Exception;

/**
 * Class Notifications
 * @package EnjinCoin
 */
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

	public static $notificationTypes = [
		self::TYPE_EVENT,
		self::TYPE_PRICE,
	];

	/**
	 * Function to get an adapter
	 * @throws Exception if no adapter found
	 * @return PusherAdapter|null
	 */
	private static function _getAdapter() {
		if (empty(self::$adapter)) {
			switch (Config::get()->notifications->method) {
				case 'pusher':
					self::$adapter = new PusherAdapter();
					break;
				default:
					throw new Exception('No adapter found');
			}
		}

		return self::$adapter;
	}

	public static function clearInstance() {
		if (!empty(self::$adapter)) {
			self::$adapter = null;
		}
	}

	/**
	 * Function to send a notification
	 * @param $channel
	 * @param $event
	 * @param $data
	 * @return mixed
	 */
	public static function notify($channel, $event, $data) {
		self::_getAdapter()->notify($channel, $event, $data);
	}

	/**
	 * Function to get the wallet channel
	 * @param string $authKey
	 * @return string
	 */
	public static function getWalletChannel(string $authKey) {
		return hash('sha512', password_hash($authKey . self::CHANNEL_WALLET, PASSWORD_BCRYPT));
	}

	/**
	 * Function to get the sdk server channel
	 * @param string $authKey
	 * @return string
	 */
	public static function getSdkServerChannel(string $authKey) {
		return hash('sha512', password_hash($authKey . self::CHANNEL_SDK_SERVER, PASSWORD_BCRYPT));
	}

	/**
	 * Function to get the sdk client channel
	 * @param string $authKey
	 * @return string
	 */
	public static function getSdkClientChannel(string $authKey) {
		return hash('sha512', password_hash($authKey . self::CHANNEL_SDK_CLIENT, PASSWORD_BCRYPT));
	}

	/**
	 * Function to get client info
	 * @return mixed
	 */
	public static function getClientInfo() {
		return self::_getAdapter()->getClientInfo();
	}
}
