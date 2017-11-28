<?php
namespace EnjinCoin\Notifications;

interface INotifications {
	public function notify($channel, $event, $data);
}
