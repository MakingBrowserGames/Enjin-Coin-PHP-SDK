<?php
namespace EnjinCoin\Notifications;

use EnjinCoin\Config;
use EnjinCoin\EventTypes;
use Pusher;

class PusherAdapter implements INotifications {
	private $pusher;

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

	public function notify($channel, $event, $data) {
		return $this->pusher->trigger(
			$channel,
			EventTypes::$event_types[$event],
			$data
		);
	}

	public function getClientInfo() {
		return [
			'app_key' => Config::get()->notifications->pusher->app_key,
			'cluster' => Config::get()->notifications->pusher->cluster
		];
	}
}
