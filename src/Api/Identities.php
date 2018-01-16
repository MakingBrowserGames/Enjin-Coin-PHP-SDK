<?php
namespace EnjinCoin\Api;

use EnjinCoin\Auth;
use EnjinCoin\EventTypes;
use PHPUnit\Runner\Exception;
use Zend;
use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;
use RandomLib;

/**
 * Class Identities
 * @package EnjinCoin\Api
 */
class Identities extends ApiBase {

	/**
     * Retrieve identities, filtered by various parameters
     * @param array $identity
     * @param bool $linked
     * @param int|null $afterIdentityId
     * @param int $limit
     * @param bool $extraFields
     * @return mixed
     */
	public function get(array $identity = [], bool $linked = false, int $afterIdentityId = null, int $limit = 50, $extraFields = false) {
		$select = $this->db->select()
			->from('identities')
			->join('identity_values', 'identities.identity_id = identity_values.identity_id', ['value'], Zend\Db\Sql\Select::JOIN_LEFT)
			->join('identity_fields', 'identity_fields.field_id = identity_values.field_id', ['key'], Zend\Db\Sql\Select::JOIN_LEFT);

		if (!$extraFields) {
			$select->columns(['identity_id', 'ethereum_address']);
		}

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

		if ($afterIdentityId) {
			$select->where->greaterThan('identities.identity_id', $afterIdentityId);
		}

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
			$selectResults = Db::query($select)->toArray();
			foreach ($selectResults as $selectResult) {
				if (in_array($selectResult['key'], ['identity_id', 'ethereum_address', 'identity_code'])) {
					continue;
				}
				$ident[$selectResult['key']] = $selectResult['value'];
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

		$identityCode = $this->_generateLinkingCode();
		$insert->values(['identity_code' => $identityCode], $insert::VALUES_MERGE);

		if (!empty($identity['ethereum_address'])) {
			$insert->values(['ethereum_address' => $identity['ethereum_address']], $insert::VALUES_MERGE);
		}
		$results = Db::query($insert);
		$identityId = $results->getGeneratedValue();

		// Insert Identity Fields & Values
		foreach ($identity as $key => $value) {
			if (in_array($key, ['identity_id', 'identity_code', 'ethereum_address'])) {
				continue;
			}

			$field = $this->field($key);
			$insert = $this->db->insert('identity_values');
			$insert->values(['identity_id' => $identityId], $insert::VALUES_MERGE);
			$insert->values(['field_id' => $field['field_id']], $insert::VALUES_MERGE);
			$insert->values(['value' => $value], $insert::VALUES_MERGE);
			Db::query($insert);
		}

		(new Events)->create(Auth::appId(), EventTypes::IDENTITY_CREATED, ['identity' => ['identity_id' => $identityId], 'identity_code' => $identityCode]);

		return [
			'identity_id' => $identityId,
			'identity_code' => $identityCode
		];
	}

	/**
	 * Generate a readable string using all upper case letters that are easy to recognize
	 * @return string
	 */
	private function _generateLinkingCode() {
		$code = '';
		$readableCharachters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
		for ($i = 0; $i < 6; $i++) {
			$code .= $readableCharachters[mt_rand(0, strlen($readableCharachters) - 1)];
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
		$existingField = $results->current();

		if (!empty($existingField)) {
			return $existingField;
		}

		$insert = $this->db->insert('identity_fields');
		$insert->values([
			'app_id' => Auth::appId(),
			'key' => $key,
			'searchable' => $searchable,
			'displayable' => $displayable,
			'unique' => $unique,
		], $insert::VALUES_MERGE);

		$results = Db::query($insert);
		$fieldId = $results->getGeneratedValue();

		$select = $this->db->select()
			->from('identity_fields')
			->where([
				'field_id' => $fieldId,
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

		foreach ($identities as $identity) {
			$delete = $this->db->delete('identities');
			$delete->where(['identity_id' => $identity['identity_id']]);

			// Event must be called before deletion
			(new Events)->create(Auth::appId(), EventTypes::IDENTITY_DELETED, ['identity' => ['identity_id' => $identity['identity_id']]]);

			Db::query($delete);
		}

		return true;
	}

	/**
	 * Update Identities based on filters
	 * @param array $identity
	 * @param array $update
	 * @param bool $emitEvent
     * @throws Exception is ethereum address is already linked
	 * @return bool
	 */
	public function update($identity, $update, $emitEvent = true) {
		$identity = $this->get($identity);
		$success = false;

		// Check if any identity is already linked to this Ethereum address
		if (!empty($update['ethereum_address'])) {
			$existingAddress = $this->get(['ethereum_address' => $update['ethereum_address']]);

			foreach ($existingAddress as $value) {
				foreach ($identity as $identityValue) {
					if ($value['identity_id'] !== $identityValue['identity_id']) {
						throw new Exception('This Ethereum address is already linked');
					}
				}
			}
		}

		foreach ($identity as $identityValue) {
			if (!empty($update['ethereum_address'])) {
				$sql = $this->db->update('identities');
				$sql->where(['identity_id' => $identityValue['identity_id']]);
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
						'identity_id' => $identityValue['identity_id'],
						'field_id' => $field['field_id']
					]);
					$sql->set(['value' => $value]);
					Db::query($sql);
					$success = true;
				}
			}

			if ($emitEvent) {
				(new Events)->create(Auth::appId(), EventTypes::IDENTITY_UPDATED, ['identity' => ['identity_id' => $identityValue['identity_id']]]);
			}
		}

		return $success;
	}

	/**
	 * Link Smart Wallet to Identity using the Linking Code
	 * todo: sign identity code using eth private key
	 * todo: should store hashed auth_key for security
	 * @param string $identityCode
	 * @param string $ethereumAddress
	 * @param string $signature
	 * @return bool
	 */
	public function link(string $identityCode, string $ethereumAddress, string $signature = '') {
		$authKey = $this->_generateAuthKey();
		$success = $this->update(['identity_code' => $identityCode],
				['ethereum_address' => $ethereumAddress, 'auth_key' => $authKey, 'identity_code' => ''],
		false);

		(new Events)->create(Auth::appId(), EventTypes::IDENTITY_LINKED, ['identity' => ['ethereum_address' => $ethereumAddress]]);

		return $success;
	}

	/**
     * Private method to generate the auth key
     * @return string
     */
	private function _generateAuthKey() {
		$factory = new RandomLib\Factory;
		$generator = $factory->getMediumStrengthGenerator();
		return 'i' . $generator->generateString(39);
	}
}
