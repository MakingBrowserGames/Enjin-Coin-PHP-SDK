<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use EnjinCoin\Auth;
use EnjinCoin\Util\Db;

/**
 * Class Tokens
 * @package EnjinCoin\Api
 */
class Tokens extends ApiBase {

	/**
	 * Function to get a token
	 * @param int|null $appId
	 * @param int|null $afterTokenId
	 * @param int $limit
	 * @param int|null $tokenId
	 * @return mixed
	 */
	public function get(int $appId = null, int $afterTokenId = null, int $limit = 50, int $tokenId = null) {
		$select = $this->db->select()
			->from('tokens')
			->order('token_id asc')
			->limit($limit);

		if (empty($appId) && empty($tokenId) && Auth::appId() > 0) {
			$appId = Auth::appId();
		}

		if (!empty($tokenId)) {
			$select->where(['token_id' => $tokenId]);
		}

		if (!empty($appId)) {
			$select->where(['app_id' => $appId]);
		}

		if (!empty($afterTokenId)) {
			$select->where->greaterThan('token_id', $afterTokenId);
		}

		$results = Db::query($select);
		$output = $results->toArray();

		return $output;
	}

	/**
	 * @param int $tokenId
	 * @return boolFunction to add a token
	 */
	public function addToken(int $tokenId) {
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
			'token_id' => $tokenId,
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

	/**
	 * Function to remove a token
	 * @param int $tokenId
	 * @return bool
	 */
	public function removeToken(int $tokenId) {
		$delete = $this->db->delete('tokens');
		$delete->where(['token_id' => $tokenId]);
		Db::query($delete);
		return true;
	}

	/**
	 * Function to get the balance
	 * @param array $identity
	 * @param $tokenIds
	 * @return array
	 */
	public function getBalance(array $identity, $tokenIds = null) {
		/**
		 * @todo remove mock request
		 * @return array
		 */
		if (!empty($identity['identity_id']) && $identity['identity_id'] === 1) {
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
