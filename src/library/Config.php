<?php
namespace EnjinCoin;
use Zend;

class Config
{
	private static $config = null;

	public static function get()
	{
		if(empty(self::$config)) {
			$json_file = file_get_contents(__DIR__ . '/../../config/config.json');
			self::$config = Zend\Json\Decoder::decode($json_file, false);
		}

		return self::$config;
	}
}
