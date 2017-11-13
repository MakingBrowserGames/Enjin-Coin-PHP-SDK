<?php

class Events extends Api
{
    public function get($event_id = null, $app_id = null, $identity = null, $after_event_id = null, $before_event_id = null, $limit = 50)
    {
        $select = $this->db->select()
            ->from('events');

        if(!empty($event_id))
            $select->where(['event_id' => $event_id]);

        if(!empty($app_id))
            $select->where(['app_id' => $app_id]);

        if(!empty($identity))
            $select->where(['app_id' => $app_id]);

        if(!empty($before_event_id))
            $select->where->lessThan('event_id', $event_id);

        if(!empty($after_event_id))
            $select->where->greaterThan('event_id', $event_id);

        $results = Db::query($select);
        return $results->toArray();
    }
}