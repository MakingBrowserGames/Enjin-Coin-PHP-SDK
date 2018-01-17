<?php
namespace EnjinCoin\Notifications;

use EnjinCoin\Config;
use EnjinCoin\EventTypes;
use Pusher;

/**
 * Class PusherAdapter
 * @package EnjinCoin\Notifications
 */
class PusherAdapter implements INotifications {
	private $pusher;

	/**
	 * PusherAdapter constructor.
	 */
	public function __construct() {
		$this->pusher = new Pusher\Pusher(
			Config::get()->notifications->pusher->app_key,
			Config::get()->notifications->pusher->app_secret,
			Config::get()->notifications->pusher->app_id,
			array(
				'encrypted' => Config::get()->notifications->pusher->encrypted,
				'cluster' => Config::get()->notifications->pusher->cluster,
			)
		);
	}

	/**
	 * Function to send a notification
	 * @param $channel
	 * @param $event
	 * @param $data
	 * @return mixed
	 */
	public function notify($channel, $event, $data) {
		return $this->pusher->trigger(
			$channel,
			EventTypes::$eventTypes[$event],
			$data
		);
	}

	/**
	 * Function to get client info
	 * @return mixed
	 */
	public function getClientInfo() {
		return [
			'app_key' => Config::get()->notifications->pusher->app_key,
			'cluster' => Config::get()->notifications->pusher->cluster
		];
	}
}
