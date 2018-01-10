<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Tokens;
use EnjinCoin\Api\Apps;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;
/**
 * @covers EnjinCoin\Api\Tokens
 */	
final class TokensTest extends TestCase {

/**
* TBD damien - better tests once we can create a token
* Once we can create a token - go over all tests and ensure that they pass - these stubs shall do for now
*/

	protected $app_id = '';
	protected $app_name = '';
	protected $app_auth_key = '';

	
	//Setup method called before every method 
	protected function setUp(): void {

		$this->app_name = 'testApp' . rand(100000000, 999999999);

		$api = new Apps();
		$result = $api->create($this->app_name);
		
		$this->app_id = $result['app_id'];
		$this->app_name = $result['name'];
		$this->app_auth_key = $result['app_auth_key'];
	}


	public function testGet_AppIdSet(): void {
		$api = new Tokens();
		$result = $api->get($this->app_id);

		$this->assertEmpty($result);
	}
	public function testGet_AppIdNotSet(): void {
		$api = new Tokens();
		$result = $api->get();

		$this->assertEmpty($result);
	}
	
	public function testGet_AfterTokenIdSet(): void {
		$after_token_id = 1;
		$api = new Tokens();
		$result = $api->get($this->app_id, $after_token_id);

		$this->assertEmpty($result);
	}
	
		
	public function testGet_LimitSet(): void {
		$after_token_id = 1;
		$limit = 100;
		$api = new Tokens();
		$result = $api->get($this->app_id, $after_token_id, $limit);

		$this->assertEmpty($result);
	}
	
	public function testGet_TokenIdSet(): void {
		$after_token_id = 1;
		$limit = 100;
		$token_id = 1;
		$api = new Tokens();
		$result = $api->get($this->app_id, $after_token_id, $limit, $token_id);

		$this->assertEmpty($result);
	}
	public function testGetBalance_IdentityArrayEmpty(): void {
		$api = new Tokens();
		$result = $api->getBalance([]);

		$this->assertEmpty($result);
	}
	public function testGetBalance_IdentityNotEmptyIdIs0(): void {
		$api = new Tokens();
		$this->identity_id = 0;
		$result = $api->getBalance(['identity_id' => $this->identity_id]);

		$this->assertEmpty($result);
	}
	public function testGetBalance_IdentityNotEmptyIdIs1(): void {
		$api = new Tokens();
		$this->identity_id = 1;
		$result = $api->getBalance(['identity_id' => $this->identity_id]);

		$this->assertNotEmpty($result);
		$this->assertArrayHasKey('ENJ', $result);
		$this->assertArrayHasKey('1', $result);
		$this->assertArrayHasKey('2', $result);
		$this->assertArrayHasKey('3', $result);
		$this->assertArrayHasKey('4', $result);
	}
}
