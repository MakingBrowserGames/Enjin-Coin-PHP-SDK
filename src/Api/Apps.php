<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;
use RandomLib;
use PHPUnit\Runner\Exception;

/**
 * Class Apps
 * @package EnjinCoin\Api
 */
class Apps extends ApiBase {
	/**
	 * Retrieve an App by its ID
	 * @param int $appId
	 * @return mixed
	 */
	public function get(int $appId) {
		$select = $this->db->select()
			->from('apps')
			->columns(['app_id', 'name'])
			->where(['app_id' => $appId]);

		$results = Db::query($select);
		return $results->current();
	}

	/**
	 * Retrieve an App by its auth key
	 * @param int $appAuthKey
	 * @return mixed
	 */
	public function getByKey(string $appAuthKey) {
		$select = $this->db->select()
			->from('apps')
			->columns(['app_id', 'name'])
			->where(['app_auth_key' => $appAuthKey]);

		$results = Db::query($select);
		return $results->current();
	}

	/**
	 * Create a new App
	 * todo: should store hashed app_auth_key for security
	 * @param string $name
	 * @throws Exception if name is empty
	 * @return array
	 */
	public function create(string $name) {
		$name = trim($name);
		if (empty($name)) {
			throw new Exception('Name must not be empty');
		}

		$appAuthKey = $this->_generateAuthKey();

		$insert = $this->db->insert('apps');
		$insert->values(['name' => $name, 'app_auth_key' => $appAuthKey], $insert::VALUES_MERGE);

		$results = Db::query($insert);
		$appId = $results->getGeneratedValue();

		return [
			'app_id' => $appId,
			'name' => $name,
			'app_auth_key' => $appAuthKey,
		];
	}

	/**
	 * Update the App
	 * @param int $appId
	 * @param string $name
	 * @throws Exception if name is empty
	 * @return bool
	 */
	public function update(int $appId, string $name) {
		$name = trim($name);
		if (empty($name)) {
			throw new Exception('Name must not be empty');
		}

		$sql = $this->db->update('apps');
		$sql->set(['name' => $name]);
		$sql->where(['app_id' => $appId]);
		Db::query($sql);

		return true;
	}

	/**
	 * Delete the App
	 * @param int $appId
	 * @return bool
	 */
	public function delete(int $appId) {
		$sql = $this->db->delete('apps');
		$sql->where(['app_id' => $appId]);
		Db::query($sql);

		return true;
	}

	private function _generateAuthKey() {
		$factory = new RandomLib\Factory;
		$generator = $factory->getMediumStrengthGenerator();
		return 'a' . $generator->generateString(39);
	}
}