<?php
namespace EnjinCoin;

class Events extends Api
{
	const PRUNE_DAYS = 30;

	const UNKNOWN_EVENT				= 0;
	const TXR_PENDING				= 1;
	const TXR_CANCELED_USER			= 2;
	const TXR_CANCELLED_PLATFORM	= 3;
	const TXR_ACCEPTED				= 4;
	const TX_BROADCASTED			= 5;
	const TX_EXECUTED				= 6;
	const TX_CONFIRMED				= 7;
	const IDENTITY_CREATED			= 8;
	const IDENTITY_LINKED			= 9;
	const IDENTITY_UPDATED			= 10;
	const IDENTITY_DELETED			= 11;

	public $event_types = [
		self::UNKNOWN_EVENT			=> 'unknown',
		self::TXR_PENDING			=> 'txr_pending',
		self::TXR_CANCELED_USER		=> 'txr_cancelled_user',
		self::TXR_CANCELLED_PLATFORM=> 'txr_cancelled_platform',
		self::TXR_ACCEPTED			=> 'txr_accepted',
		self::TX_BROADCASTED		=> 'tx_broadcasted',
		self::TX_EXECUTED			=> 'tx_executed',
		self::TX_CONFIRMED			=> 'tx_confirmed',
		self::IDENTITY_CREATED		=> 'identity_created',
		self::IDENTITY_LINKED		=> 'identity_linked',
		self::IDENTITY_UPDATED		=> 'identity_updated',
		self::IDENTITY_DELETED		=> 'identity_deleted',
	];

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
    public function get($event_id = null, $app_id = null, $identity = null, $after_event_id = null, $before_event_id = null, $limit = 50)
    {
        $select = $this->db->select()
            ->from('events')
			->order('event_id desc')
			->limit($limit);

        if(!empty($event_id))
            $select->where(['event_id' => $event_id]);

        if(!empty($app_id))
            $select->where(['app_id' => $app_id]);

        if(!empty($identity)) {
			$select->join('identities', 'events.identity_id = identities.identity_id', null);
			foreach($identity as $key => $value) {
				$select->where(['identities.' . $key => $value]);
			}
		}

        if(!empty($before_event_id))
            $select->where->lessThan('event_id', $before_event_id);

        if(!empty($after_event_id))
            $select->where->greaterThan('event_id', $after_event_id);

        $results = Db::query($select);
        return $results->toArray();
    }

	/**
	 * Delete old events from the database
	 * @return bool
	 */
    public function prune()
	{
		$delete = $this->db->delete('events');
		$delete->where('timestamp < unix_timestamp() - (86400 * ' . self::PRUNE_DAYS . ')');
		Db::query($delete);
		return true;
	}
}