<?php

class Events extends Api
{
    public function get($event_id)
    {
        $select = $this->db->select()
            ->from('apps')
            ->where(['event_id' => $event_id]);

        $results = Db::query($select);
        return $results->toArray();
    }
}