<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;

class Apps extends ApiBase {
	/**
	 * Retrieve an App by its ID
	 * @param int $app_id
	 * @return mixed
	 */
	public function get(int $app_id) {
		$select = $this->db->select()
			->from('apps')
			->where(['app_id' => $app_id]);

		$results = Db::query($select);
		return $results->toArray();
	}

	/**
	 * Create a new App
	 * @param string $name
	 * @return array
	 */
	public function create(string $name) {
		$name = trim($name);
		if (empty($name)) throw new Exception('Name must not be empty');

		$insert = $this->db->insert('apps');
		$insert->values(['name' => $name], $insert::VALUES_MERGE);
		$results = Db::query($insert);
		$app_id = $results->getGeneratedValue();

		return [
			'app_id' => $app_id,
			'name' => $name,
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
}