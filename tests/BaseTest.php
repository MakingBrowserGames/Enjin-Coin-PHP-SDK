<?php

namespace EnjinCoin\Test;


use EnjinCoin\Config;
use EnjinCoin\Util\Db;
use PHPUnit\Framework\TestCase;
use Zend\Db\Adapter\Adapter;

abstract class BaseTest extends TestCase {

	protected function setUp(): void {
		$results = Db::getInstance()->getAdapter()->query('SHOW TABLES', Adapter::QUERY_MODE_EXECUTE);

		Db::getInstance()->getAdapter()->query('SET FOREIGN_KEY_CHECKS = 0', Adapter::QUERY_MODE_EXECUTE);
		foreach ($results as $result) {
			$table = $result['Tables_in_' . Config::get()->db->database];
			$delete = Db::getInstance()->delete($table)->where('1=1');
			Db::query($delete);
		}
		Db::getInstance()->getAdapter()->query('SET FOREIGN_KEY_CHECKS = 1', Adapter::QUERY_MODE_EXECUTE);
	}

}