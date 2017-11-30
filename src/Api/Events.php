<?php
namespace EnjinCoin\Api;

use EnjinCoin;
use EnjinCoin\Util\Db;
use EnjinCoin\ApiBase;
use EnjinCoin\Notifications;
use PHPUnit\Runner\Exception;
use Zend;

class Events extends ApiBase {
	const PRUNE_DAYS = 30;

	/**
	 * Get a list of events
	 * @param int $event_id
	 * @param int $app_id
	 * @param array $identity
	 * @param int $after_event_id
	 * @param int $before_event_id
	 * @param int $limit
	 * @return mixed
	 */
	public function get($event_id = null, $app_id = null, $identity = null, $after_event_id = null, $before_event_id = null, $limit = 50) {
		$select = $this->db->select()
			->from('events')
			->order('event_id desc')
			->limit($limit);

		if (!empty($event_id))
			$select->where(['event_id' => $event_id]);

		if (!empty($app_id))
			$select->where(['app_id' => $app_id]);

		if (!empty($identity)) {
			$select->join('identities', 'events.identity_id = identities.identity_id', null);
			foreach ($identity as $key => $value) {
				$select->where(['identities.' . $key => $value]);
			}
		}

		if (!empty($before_event_id))
			$select->where->lessThan('event_id', $before_event_id);

		if (!empty($after_event_id))
			$select->where->greaterThan('event_id', $after_event_id);

		$results = Db::query($select);
		$output = $results->toArray();
		foreach($output as &$value) {
			$value['data'] = Zend\Json\Decoder::decode($value['data']);
		}

		return $output;
	}

	public function create(int $app_id, $event_type, $data) {
		// Validate App ID
		if ($app_id != 0) {
			$apps = new Apps();
			$app = $apps->get($app_id);
			if (empty($app['app_id'])) throw new Exception('App ID does not exist');
		}

		// Validate Identity
		if (!empty($data['identity'])) {
			$identities = new Identities();
			$ident = $identities->get($data['identity']);
			if (empty($ident['identity_id'])) throw new Exception('Identity does not exist');
		}

		// Validate Event Type
		$event_types = new EnjinCoin\EventTypes;
		$event = $event_types->callEvent(
			$event_type,
			$app_id,
			!empty($ident) ? $ident['identity_id'] : 0,
			$data
		);
		if(empty($event)) throw new Exception('Invalid event type');

		// Insert the Event
		$insert = $this->db->insert('events');
		$insert->values([
			'timestamp' => time(),
			'app_id' => $app_id,
			'identity_id' => !empty($ident) ? $ident['identity_id'] : 0,
			'event_type' => $event_type,
			'data' => Zend\Json\Encoder::encode($event),
		], $insert::VALUES_MERGE);

		// Notify
		// todo: retrieve a notification channels map for each event
		Notifications::notify(Notifications::CHANNEL_GAME_SERVER, $event_type, $event);
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

	public function test() {
		$eth = new EnjinCoin\Ethereum;
		$eth->test();
	}
}
