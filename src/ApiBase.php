<?php
namespace EnjinCoin;

use EnjinCoin\Util\Db;

/**
 * Class ApiBase
 * @package EnjinCoin
 */
class ApiBase {
	public $db;

	/**
	 * ApiBase constructor.
	 */
	public function __construct() {
		$this->db = Db::getDatabase();
	}
}
