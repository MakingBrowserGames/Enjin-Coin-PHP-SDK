<?php

class Identities extends Api
{
    public function get($identity = [], $linked = true, $after_identity_id = null, $limit = 50)
    {
        $select = $this->db->select()
            ->from('identities')
            ->join('identity_fields', 'identities.identity_id = identity_fields.identity_id')
            ->join('identity_values', 'identity_fields.identity_id = identity_values.identity_id AND identity_fields.field_id = identity_values.field_id');

        foreach ($identity as $key => $value) {
            switch ($key) {
                case 'identity_id':
                case 'ethereum_address':
                case 'linking_code':
                    $select->where(['identities.' . $key => $value]);
                    break;
                default:
                    $select->where(['identity_fields.key' => $key]);
                    $select->where(['identity_values.value' => $value]);
                    break;
            }
        }

        if ($after_identity_id)
            $select->where(['after_identity_id' => $after_identity_id]);

        $select->limit($limit);

        $results = Db::query($select);
        return $results;
    }

    public function create($identity)
    {
        $insert = $this->db->insert('identities');
        foreach ($identity as $key => $value) {
            if (in_array($key, ['identity_id', 'linking_code']))
                continue;

            $insert->values([$key => $value], $insert::VALUES_MERGE);
        }
    }

    public function delete($identity)
    {
        $this->db->delete('identities');
    }

    public function update($identity)
    {
        $this->db->update('identities');
    }
}
