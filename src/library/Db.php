<?php

/**
 * Db Singleton
 */
class Db
{
	public static $adapter;
	public static $sql;

	public static function getInstance() {
		if(empty(self::$adapter)) {
			self::$adapter = new Zend\Db\Adapter\Adapter([
				'driver' => 'Mysqli',
				'database' => 'enjin_coin',
				'username' => 'developer',
				'password' => 'dev-password'
			]);

			self::$sql = $sql = new Zend\Db\Sql\Sql(self::$adapter);
		}

		return self::$sql;
	}

	public static function query($select) {
		return self::$adapter->query(
		    self::$sql->buildSqlString($select),
            (self::$adapter)::QUERY_MODE_EXECUTE
        );
	}
}