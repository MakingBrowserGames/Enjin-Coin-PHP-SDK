<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use EnjinCoin\Auth;
use EnjinCoin\Util\Db;

class Tokens extends ApiBase {
	public function get(int $app_id = null, int $after_token_id = null, int $limit = 50, int $token_id = null) {
		$select = $this->db->select()
			->from('tokens')
			->order('token_id asc')
			->limit($limit);

		if (empty($app_id) && empty($token_id) && Auth::appId() > 0) {
			$app_id = Auth::appId();
		}

		if (!empty($token_id)) {
			$select->where(['token_id' => $token_id]);
		}

		if (!empty($app_id)) {
			$select->where(['app_id' => $app_id]);
		}

		if (!empty($after_token_id)) {
			$select->where->greaterThan('token_id', $after_token_id);
		}

		$results = Db::query($select);
		$output = $results->toArray();

		return $output;
	}

	public function addToken(int $token_id) {
		// @todo fetch data from CustomTokens contract on the blockchain
		$result = ['creator' => null,
		'adapter' => null,
		'name' => null,
		'icon' => null,
		'totalSupply' => null,
		'exchangeRate' => null,
		'decimals' => 0,
		'maxMeltFee' => null,
		'meltFee' => null,
		'transferable' => 1];

		$data = [
			'token_id' => $token_id,
			'app_id' => Auth::appId(),
			'creator' => $result['creator'],
			'adapter' => $result['adapter'],
			'name' => $result['name'],
			'icon' => $result['icon'],
			'totalSupply' => $result['totalSupply'],
			'exchangeRate' => $result['exchangeRate'],
			'decimals' => $result['decimals'],
			'maxMeltFee' => $result['maxMeltFee'],
			'meltFee' => $result['meltFee'],
			'transferable' => $result['transferable'],
		];

		$insert = $this->db->insert('tokens');
		$insert->values($data, $insert::VALUES_MERGE);
		Db::query($insert);
		return true;
	}

	public function removeToken(int $token_id) {
		$delete = $this->db->delete('tokens');
		$delete->where(['token_id' => $token_id]);
		Db::query($delete);
		return true;
	}

	public function getBalance(array $identity, $token_ids = null) {
		/**
		 * @todo remove mock request
		 */
		if (!empty($identity['identity_id']) && $identity['identity_id'] == 1) {
			return [
				'ENJ' => '50034.871212583712984734',
				'1' => '1',
				'2' => '1',
				'3' => '5',
				'4' => '129.25',
			];
		}

		return [];
	}
}
