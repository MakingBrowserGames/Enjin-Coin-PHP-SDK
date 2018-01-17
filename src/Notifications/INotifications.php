<?php
namespace EnjinCoin\Notifications;

/**
 * Interface INotifications
 * @package EnjinCoin\Notifications
 */
interface INotifications {

	/**
	 * Function to send a notification
	 * @param $channel
	 * @param $event
	 * @param $data
	 * @return mixed
	 */
	public function notify($channel, $event, $data);

	/**
	 * Function to get client info
	 * @return mixed
	 */
	public function getClientInfo();
}
