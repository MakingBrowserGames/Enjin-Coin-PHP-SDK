<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;
use RandomLib;

class Apps extends ApiBase {
	/**
	 * Retrieve an App by its ID
	 * @param int $app_id
	 * @return mixed
	 */
	public function get(int $app_id) {
		$select = $this->db->select()
			->from('apps')
			->columns(['app_id', 'name'])
			->where(['app_id' => $app_id]);

		$results = Db::query($select);
		return $results->current();
	}

	/**
	 * Retrieve an App by its auth key
	 * @param int $app_auth_key
	 * @return mixed
	 */
	public function getByKey(string $app_auth_key) {
		$select = $this->db->select()
			->from('apps')
			->columns(['app_id', 'name'])
			->where(['app_auth_key' => $app_auth_key]);

		$results = Db::query($select);
		return $results->current();
	}

	/**
	 * Create a new App
	 * @param string $name
	 * @return array
	 */
	public function create(string $name) {
		$name = trim($name);
		if (empty($name)) throw new Exception('Name must not be empty');

		$app_auth_key = $this->generateAuthKey();

		$insert = $this->db->insert('apps');
		$insert->values(['name' => $name, 'app_auth_key' => $app_auth_key], $insert::VALUES_MERGE);

		$results = Db::query($insert);
		$app_id = $results->getGeneratedValue();

		return [
			'app_id' => $app_id,
			'name' => $name,
			'app_auth_key' => $app_auth_key,
		];
	}

	/**
	 * Update the App
	 * @param int $app_id
	 * @param string $name
	 * @return bool
	 */
	public function update(int $app_id, string $name) {
		$name = trim($name);
		if (empty($name)) throw new Exception('Name must not be empty');

		$sql = $this->db->update('apps');
		$sql->set(['name' => $name]);
		$sql->where(['app_id' => $app_id]);
		Db::query($sql);

		return true;
	}

	/**
	 * Delete the App
	 * @param int $app_id
	 * @return bool
	 */
	public function delete(int $app_id) {
		$sql = $this->db->delete('apps');
		$sql->where(['app_id' => $app_id]);
		Db::query($sql);

		return true;
	}

	private function generateAuthKey() {
		$factory = new RandomLib\Factory;
		$generator = $factory->getMediumStrengthGenerator();
		return 'a' . $generator->generateString(36);
	}
}