<?php
namespace EnjinCoin;

class Api
{
	public $db;

	public function __construct()
	{
		$this->db = Db::getInstance();
	}
}
