<?php

class Identities extends Api
{
    public function get($identity = array())
    {
		$select = $this->db->select()
			->from('identities')
			->join('identity_fields', 'identities.identity_id = identity_fields.identity_id')
			->join('identity_values', 'identity_fields.identity_id = identity_values.identity_id AND identity_fields.field_id = identity_values.field_id');

		foreach($identity as $key => $value) {
			switch($key) {
				case 'identity_id':
					$select->where(array('identities.identity_id = ?' => $identity['identity_id']));
					break;
				case 'ethereum_address':
					$select->where(array('identities.ethereum_address = ?' => $identity['ethereum_address']));
					break;
				case 'linking_code':
					$select->where(array('identities.linking_code = ?' => $identity['linking_code']));
					break;
				default:
					$select->where(array('identity_fields.key = ?' => $key));
					$select->where(array('identity_values.value = ?' => $value));
					break;
			}
		}
		$results = Db::query($select);

		return $results;
    }

    /* @todo: do this in "get" for simplicity
	public function list($identity = array(), $linked = true, $after_identity_id = null, $limit = 50)
	{
	}
    */

	public function create($identity)
	{
		$this->db->insert('identities');
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
