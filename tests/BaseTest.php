<?php

namespace EnjinCoin\Test;


use EnjinCoin\Auth;
use EnjinCoin\Config;
use EnjinCoin\Util\Db;
use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\Adapter;

abstract class BaseTest extends TestCase {

	protected function setUp(): void {
		Auth::clearInstance();
		Db::clearInstance();

		$results = Db::getDatabase()->getAdapter()->query('SHOW TABLES', Adapter::QUERY_MODE_EXECUTE);

		Db::getDatabase()->getAdapter()->query('SET FOREIGN_KEY_CHECKS = 0', Adapter::QUERY_MODE_EXECUTE);
		foreach ($results as $result) {
			$table = $result['Tables_in_' . Config::get()->db->database];
			$delete = Db::getDatabase()->delete($table)->where('1=1');
			Db::query($delete);
		}
		Db::getDatabase()->getAdapter()->query('SET FOREIGN_KEY_CHECKS = 1', Adapter::QUERY_MODE_EXECUTE);
	}

}