<?php
namespace EnjinCoin\Api;
use Zend;
use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;

class Identities extends ApiBase
{
    /**
     * Retrieve identities, filtered by various parameters
     * @param array $identity
     * @param bool $linked
     * @param int $after_identity_id
     * @param int $limit
     * @return mixed
     */
    public function get(array $identity = [], bool $linked = true, int $after_identity_id = null, int $limit = 50)
    {
        $select = $this->db->select()
            ->from('identities')
            ->join('identity_values', 'identities.identity_id = identity_values.identity_id', Zend\Db\Sql\Select::SQL_STAR, Zend\Db\Sql\Select::JOIN_LEFT)
            ->join('identity_fields', 'identity_fields.field_id = identity_values.field_id', Zend\Db\Sql\Select::SQL_STAR, Zend\Db\Sql\Select::JOIN_LEFT);

        if($linked) {
            $select->where("ethereum_address != ''");
        }

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
        $idents = $results->toArray();

        foreach($idents as &$ident) {
            unset($ident['key']);
            unset($ident['value']);

            $select = $this->db->select()
                ->from('identity_values')
                ->join('identity_fields', 'identity_fields.field_id = identity_values.field_id', Zend\Db\Sql\Select::SQL_STAR, Zend\Db\Sql\Select::JOIN_INNER)
                ->where(['identity_id' => $ident['identity_id']]);
            $v_result = Db::query($select)->toArray();
            foreach($v_result as $v) {
                if(in_array($v['key'], ['identity_id', 'ethereum_address', 'linking_code'])) continue;
                $ident[$v['key']] = $v['value'];
            }
        }

        return $idents;
    }

    /**
     * Create a new identity, returning the Identity ID and Linking Code
     * @param $identity
     * @return array
     */
    public function create($identity)
    {
        /*
         * Insert Identity
         */
        $insert = $this->db->insert('identities');

        $linking_code = $this->generateLinkingCode();
        $insert->values(['linking_code' => $linking_code], $insert::VALUES_MERGE);

        if(!empty($identity['ethereum_address'])) {
            $insert->values(['ethereum_address' => $identity['ethereum_address']], $insert::VALUES_MERGE);
        }
        $results = Db::query($insert);
        $identity_id = $results->getGeneratedValue();

        /*
         * Insert Identity Fields & Values
         */
        foreach ($identity as $key => $value) {
            if (in_array($key, ['identity_id', 'linking_code', 'ethereum_address'])) continue;

            $field = $this->field($key);
            $insert = $this->db->insert('identity_values');
            $insert->values(['identity_id' => $identity_id], $insert::VALUES_MERGE);
            $insert->values(['field_id' => $field['field_id']], $insert::VALUES_MERGE);
            $insert->values(['value' => $value], $insert::VALUES_MERGE);
            Db::query($insert);
        }

        return [
            'identity_id' => $identity_id,
            'linking_code' => $linking_code
        ];
    }

    /**
     * Generate a readable string using all upper case letters that are easy to recognize
     * @return string
     */
    private function generateLinkingCode() {
        $code = '';
        $readable_characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        for ($i = 0; $i < 6; $i++) {
            $code .= $readable_characters[mt_rand(0, strlen($readable_characters) - 1)];
        }
        return $code;
    }

    /**
     * Insert or fetch an identity field type
     * @param $key
     * @param int $searchable
     * @param int $displayable
     * @param int $unique
     * @return mixed
     */
    public function field($key, $searchable = 1, $displayable = 1, $unique = 1) {
        $select = $this->db->select()
            ->from('identity_fields')
            ->where([
                'key' => $key,
            ]);

        $results = Db::query($select);
        $existing_field = $results->current();
        if(!empty($existing_field)) return $existing_field;

        $insert = $this->db->insert('identity_fields');
        $insert->values([
            'key' => $key,
            'searchable' => $searchable,
            'displayable' => $displayable,
            'unique' => $unique,
        ], $insert::VALUES_MERGE);

        $results = Db::query($insert);
        $field_id = $results->getGeneratedValue();

        $select = $this->db->select()
            ->from('identity_fields')
            ->where([
                'field_id' => $field_id,
            ]);

        $results = Db::query($select);
        $field = $results->current();
        return $field;
    }

    /**
     * Delete Identities based on filters
     * @param array $identity
     * @return bool
     */
    public function delete($identity)
    {
        $delete = $this->db->delete('identities');
        $delete->where($identity);
        Db::query($delete);
        return true;
    }

    /**
     * Update Identities based on filters
     * @param $identity
     * @param $update
     * @return bool
     */
    public function update($identity, $update)
    {
        $identity = $this->get($identity);

        foreach($identity as $i) {
            if (!empty($update['ethereum_address'])) {
                $sql = $this->db->update('identities');
                $sql->where(['identity_id' => $i['identity_id']]);
                $sql->set(['ethereum_address' => $i['ethereum_address']]);
                Db::query($sql);
                unset($update['ethereum_address']);
            }

            if(!empty($update)) {
                foreach($update as $key => $value) {
                    $field = $this->field($key);
                    $sql = $this->db->update('identity_values');
                    $sql->where([
                        'identity_id' => $i['identity_id'],
                        'field_id' => $field['field_id']
                    ]);
                    $sql->set(['value' => $value]);
                    Db::query($sql);
                }
            }
        }

        return true;
    }
}
