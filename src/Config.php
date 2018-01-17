<?php
namespace EnjinCoin;

use Zend;

/**
 * Class Config
 * @package EnjinCoin
 */
class Config {
	private static $config = null;

	/**
	 * Function to get the config
	 * @return mixed|null
	 */
	public static function get() {
		if (empty(self::$config)) {
			$jsonFile = file_get_contents(__DIR__ . '/../config/config.json');
			self::$config = Zend\Json\Decoder::decode($jsonFile, false);
		}

		return self::$config;
	}
}
