<?php
namespace EnjinCoin\Util;

use Zend;
use EnjinCoin\Config;

/**
 * Db Singleton
 * Class Db
 * @package EnjinCoin\Util
 */
class Db {
	public static $adapter;
	public static $sql;

	/**
	 * Method to get a db instance
	 * @return Zend\Db\Sql\Sql
	 */
	public static function getInstance() {
		if (empty(self::$adapter)) {
			self::$adapter = new Zend\Db\Adapter\Adapter([
				'driver' => Config::get()->db->adapter,
				'database' => Config::get()->db->database,
				'username' => Config::get()->db->username,
				'password' => Config::get()->db->password
			]);

			self::$sql = $sql = new Zend\Db\Sql\Sql(self::$adapter);
		} 
		return self::$sql;
	}

	/**
	 * Method to perform a db query
	 * @param $select
	 * @return mixed
	 */
	public static function query($select) {
		
		return self::$adapter->query(
			self::$sql->buildSqlString($select),
			Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE
		);
	}
}
