<?php
namespace EnjinCoin\Notifications;

use EnjinCoin\Config;
use Pusher;

class PusherAdapter implements INotifications {
	private $pusher;

	public function __construct() {
		$this->pusher = new Pusher\Pusher(
			Config::get()->notifications->pusher->app_key,
			Config::get()->notifications->pusher->app_secret,
			Config::get()->notifications->pusher->app_id,
			array(
				'encrypted' => true
			)
		);
	}

	public function notify($channel, $event, $data) {
		$this->pusher->trigger(
			$channel,
			$event,
			$data
		);
	}
}
