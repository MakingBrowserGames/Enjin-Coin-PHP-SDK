<?php

require_once 'Db.php';

class Api
{
	public $db;

	public function __construct()
	{
		$this->db = Db::getInstance();
	}
}