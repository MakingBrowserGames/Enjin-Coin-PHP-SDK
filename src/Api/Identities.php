<?php
namespace EnjinCoin\Api;

use EnjinCoin\Auth;
use EnjinCoin\EventTypes;
use PHPUnit\Runner\Exception;
use Zend;
use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;

class Identities extends ApiBase {
	/**
	 * Retrieve identities, filtered by various parameters
	 * @param array $identity
	 * @param bool $linked
	 * @param int $after_identity_id
	 * @param int $limit
	 * @return mixed
	 */
	public function get(array $identity = [], bool $linked = false, int $after_identity_id = null, int $limit = 50, $extra_fields = false) {
		$select = $this->db->select()
			->from('identities')
			->join('identity_values', 'identities.identity_id = identity_values.identity_id', ['value'], Zend\Db\Sql\Select::JOIN_LEFT)
			->join('identity_fields', 'identity_fields.field_id = identity_values.field_id', ['key'], Zend\Db\Sql\Select::JOIN_LEFT);

		if(!$extra_fields) $select->columns(['identity_id', 'ethereum_address']);

		if ($linked) {
			$select->where("ethereum_address != ''");
		}

		foreach ($identity as $key => $value) {
			switch ($key) {
				case 'identity_id':
				case 'ethereum_address':
				case 'identity_code':
					$select->where(['identities.' . $key => $value]);
					break;
				default:
					$select->where(['identity_fields.key' => $key]);
					$select->where(['identity_values.value' => $value]);
					break;
			}
		}

		if ($after_identity_id)
			$select->where->greaterThan('identities.identity_id', $after_identity_id);

		$select->limit($limit);

		$results = Db::query($select);
		$idents = $results->toArray();

		foreach ($idents as &$ident) {
			unset($ident['key']);
			unset($ident['value']);

			$select = $this->db->select()
				->from('identity_values')
				->join('identity_fields', 'identity_fields.field_id = identity_values.field_id', Zend\Db\Sql\Select::SQL_STAR, Zend\Db\Sql\Select::JOIN_INNER)
				->where(['identity_id' => $ident['identity_id']]);
			$v_result = Db::query($select)->toArray();
			foreach ($v_result as $v) {
				if (in_array($v['key'], ['identity_id', 'ethereum_address', 'identity_code'])) continue;
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
	public function create(array $identity) {
		$insert = $this->db->insert('identities');

		$identity_code = $this->generateLinkingCode();
		$insert->values(['identity_code' => $identity_code], $insert::VALUES_MERGE);

		if (!empty($identity['ethereum_address'])) {
			$insert->values(['ethereum_address' => $identity['ethereum_address']], $insert::VALUES_MERGE);
		}
		$results = Db::query($insert);
		$identity_id = $results->getGeneratedValue();

        // Insert Identity Fields & Values
		foreach ($identity as $key => $value) {
			if (in_array($key, ['identity_id', 'identity_code', 'ethereum_address'])) continue;

			$field = $this->field($key);
			$insert = $this->db->insert('identity_values');
			$insert->values(['identity_id' => $identity_id], $insert::VALUES_MERGE);
			$insert->values(['field_id' => $field['field_id']], $insert::VALUES_MERGE);
			$insert->values(['value' => $value], $insert::VALUES_MERGE);
			Db::query($insert);
		}

		(new Events)->create(Auth::appId(), EventTypes::IDENTITY_CREATED, ['identity' => ['identity_id' => $identity_id], 'identity_code' => $identity_code]);

		return [
			'identity_id' => $identity_id,
			'identity_code' => $identity_code
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
	 * @param string $key
	 * @param int $searchable
	 * @param int $displayable
	 * @param int $unique
	 * @return mixed
	 */
	public function field(string $key, int $searchable = 1, int $displayable = 1, int $unique = 1) {
		$select = $this->db->select()
			->from('identity_fields')
			->where([
				'key' => $key,
			]);

		$results = Db::query($select);
		$existing_field = $results->current();
		if (!empty($existing_field)) return $existing_field;

		$insert = $this->db->insert('identity_fields');
		$insert->values([
		    'app_id' => Auth::appId(),
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
	public function delete($identity) {
        $identities = $this->get($identity);

        foreach($identities as $identity) {
            $delete = $this->db->delete('identities');
            $delete->where($identity);
            Db::query($delete);

            (new Events)->create(Auth::appId(), EventTypes::IDENTITY_DELETED, ['identity' => ['identity_id' => $identity['identity_id']]]);
        }

		return true;
	}

	/**
	 * Update Identities based on filters
	 * @param array $identity
	 * @param array $update
	 * @param bool $emit_event
	 * @return bool
	 */
	public function update($identity, $update, $emit_event = true) {
	    $identity = $this->get($identity);
        $success = false;

        // Check if any identity is already linked to this Ethereum address
        if (!empty($update['ethereum_address'])) {
            $existing_address = $this->get(['ethereum_address' => $update['ethereum_address']]);
            foreach($existing_address as $value) {
                foreach ($identity as $i) {
                    if ($value['identity_id'] != $i['identity_id']) throw new Exception('This Ethereum address is already linked');
                }
            }
        }

		foreach ($identity as $i) {
			if (!empty($update['ethereum_address'])) {
				$sql = $this->db->update('identities');
				$sql->where(['identity_id' => $i['identity_id']]);
				$sql->set([
					'ethereum_address' => $update['ethereum_address'],
					'identity_code' => ''
				]);
				Db::query($sql);
				unset($update['ethereum_address']);
                $success = true;
			}

			if (!empty($update)) {
				foreach ($update as $key => $value) {
					$field = $this->field($key);
					$sql = $this->db->update('identity_values');
					$sql->where([
						'identity_id' => $i['identity_id'],
						'field_id' => $field['field_id']
					]);
					$sql->set(['value' => $value]);
					Db::query($sql);
                    $success = true;
				}
			}
		}

		if($emit_event)
			(new Events)->create(Auth::appId(), EventTypes::IDENTITY_UPDATED, ['identity' => ['identity_id' => $identity['identity_id']]]);

		return $success;
	}

	/**
	 * Link Smart Wallet to Identity using the Linking Code
     * todo: sign identity code using eth private key
	 * @param string $identity_code
	 * @param string $ethereum_address
	 * @param string $signature
	 * @return bool
	 */
	public function link(string $identity_code, string $ethereum_address, string $signature = '') {
		$success = $this->update(['identity_code' => $identity_code], ['ethereum_address' => $ethereum_address], false);

		(new Events)->create(Auth::appId(), EventTypes::IDENTITY_LINKED, ['identity' => ['ethereum_address' => $ethereum_address]]);

		return $success;
	}
}
