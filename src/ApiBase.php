<?php
namespace EnjinCoin;

class ApiBase
{
	public $db;

	public function __construct()
	{
		$this->db = \EnjinCoin\Util\Db::getInstance();
	}
}
