<?php
namespace EnjinCoin;
use EnjinCoin\Util\Db;

class ApiBase
{
	public $db;

	public function __construct()
	{
		$this->db = Db::getInstance();
	}
}
