<?php
namespace EnjinCoin\Api;

use EnjinCoin;
use EnjinCoin\Auth;
use EnjinCoin\Util\Db;
use EnjinCoin\ApiBase;
use EnjinCoin\Notifications;
use PHPUnit\Runner\Exception;
use Zend;

/**
 * Class Events
 * @package EnjinCoin\Api
 */
class Events extends ApiBase {
	const PRUNE_DAYS = 30;

	/**
	 * Get a list of events
	 * @param int $eventId
	 * @param int $appId
	 * @param array $identity
	 * @param int $afterEventId
	 * @param int $beforeEventId
	 * @param int $limit
	 * @return mixed
	 */
	public function get($eventId = null, $appId = null, $identity = null, $afterEventId = null, $beforeEventId = null, $limit = 50) {
		$select = $this->db->select()
			->from('events')
			->order('event_id desc')
			->limit($limit);

		if (!empty($eventId)) {
			$select->where(['event_id' => $eventId]);
		}

		if (!empty($appId)) {
			$select->where(['app_id' => $appId]);
		}

		if (!empty($identity)) {
			$select->join('identities', 'events.identity_id = identities.identity_id', null);
			foreach ($identity as $key => $value) {
				$select->where(['identities.' . $key => $value]);
			}
		}

		if (!empty($beforeEventId)) {
			$select->where->lessThan('event_id', $beforeEventId);
		}

		if (!empty($afterEventId)) {
			$select->where->greaterThan('event_id', $afterEventId);
		}

		$results = Db::query($select);
		$output = $results->toArray();
		foreach ($output as &$value) {
			$value['data'] = Zend\Json\Decoder::decode($value['data']);
		}
		return $output;
	}

	/**
	 * Function to create an event
	 * @param int $appId
	 * @param $eventType
	 * @param $data
	 * @throws Exception if event type is not valid
	 */
	public function create(int $appId, $eventType, $data) {
		// Validate App ID
		if ($appId !== 0) {
			$apps = new Apps();
			$app = $apps->get($appId);
			if (empty($app['app_id'])) {
				throw new Exception('App ID does not exist');
			}
		}

		// Validate Identity
		if (!empty($data['identity'])) {
			$identities = new Identities();
			$ident = $identities->get($data['identity']);

			if (!empty($ident)) {
				$ident = reset($ident);
			}
			if (empty($ident['identity_id'])) {
				throw new Exception('Identity does not exist');
			}
		}

		// Validate Event Type
		$eventTypes = new EnjinCoin\EventTypes;
		$event = $eventTypes->callEvent(
			$eventType,
			$appId,
			!empty($ident) ? $ident['identity_id'] : 0,
			$data
		);
		if (empty($event)) {
			throw new Exception('Invalid event type');
		}

		// Insert the Event
		// @todo: shorten all "identity" fields to only include "identity_id" if event data grows too fast or becomes cumbersome
		$insert = $this->db->insert('events');
		$timestamp = time();
		$insert->values([
			'timestamp' => $timestamp,
			'app_id' => $appId,
			'identity_id' => !empty($ident) ? $ident['identity_id'] : 0,
			'event_type' => $eventType,
			'data' => Zend\Json\Encoder::encode($event),
		], $insert::VALUES_MERGE);

		$result = Db::query($insert);

		$event['event_id'] = $result->getGeneratedValue();
		$event['timestamp'] = $timestamp;

		// Notify
		// @todo: get the correct app's auth key
		Notifications::notify(Notifications::getSdkServerChannel(Auth::authKey()), $eventType, $event);
	}

	/**
	 * Delete old events from the database
	 * @return bool
	 */
	public function prune() {
		$delete = $this->db->delete('events');
		$delete->where('timestamp < unix_timestamp() - (86400 * ' . self::PRUNE_DAYS . ')');
		Db::query($delete);
		return true;
	}
}
