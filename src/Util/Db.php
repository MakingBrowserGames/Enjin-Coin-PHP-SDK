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
	private static $instance;
	private $adapter;
	private $sql;

	function __construct() {
		$this->adapter = new Zend\Db\Adapter\Adapter([
			'driver' => Config::get()->db->adapter,
			'host' => Config::get()->db->host,
			'database' => Config::get()->db->database,
			'username' => Config::get()->db->username,
			'password' => Config::get()->db->password
		]);

		$this->sql = new Zend\Db\Sql\Sql($this->adapter);
	}

	/**
	 * Method to get Db instance;
	 * @return Db
	 */
	public static function getInstance() {
		if (empty(self::$instance)) {
			self::$instance = new Db();
		}
		return self::$instance;
	}

	public static function clearInstance() {
		self::$instance = null;
	}

	/**
	 * Method to get Sql instance
	 * @return Zend\Db\Sql\Sql
	 */
	public static function getDatabase() {
		return self::getInstance()->sql;
	}

	/**
	 * Method to get Adapter instance
	 * @return Zend\Db\Adapter\Adapter
	 */
	public static function getAdapter() {
		return self::getInstance()->adapter;
	}

	/**
	 * Method to perform a db query
	 * @param $select
	 * @return mixed
	 */
	public static function query($select) {
		return self::getAdapter()->query(
			self::getDatabase()->buildSqlString($select),
			Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE
		);
	}
}
