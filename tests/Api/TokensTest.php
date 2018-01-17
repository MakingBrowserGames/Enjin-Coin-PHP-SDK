<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Tokens;
use EnjinCoin\Api\Apps;
use EnjinCoin\Auth;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;
/**
 * @covers EnjinCoin\Api\Tokens
 */	
final class TokensTest extends TestCase {

	protected $token_id = 0;
	protected $app_id = '';
	protected $app_auth_key = '';
	protected $app_name = '';

	
	//Setup method called before every method 
	protected function setUp(): void {
	    $this->app_name = 'TestApp_' . rand(1, 999999999);

        $result = (new Apps())->create($this->app_name);
        $this->app_id = $result['app_id'];
        $this->app_auth_key = $result['app_auth_key'];
        Auth::init($this->app_auth_key);

		//Add a token before each test
		$this->token_id = rand(100000000, 999999999);		
		$api = new Tokens();
		$result = $api->addToken($this->token_id);
		$this->assertTrue($result);
	}

	public function testGet_AppIdSet(): void {
		$api = new Tokens();
		$result = $api->get($this->app_id);

		$this->assertEmpty($result);
	}
	
	public function testGet_AuthHasOccuredNoTokens(): void {
		$result = Auth::init($this->app_auth_key);
		$this->assertTrue($result);
		
		$api = new Tokens();
		$result = $api->get();
		$this->assertEmpty($result);
	}

	public function testGet_AppIdNotSet(): void {
		$api = new Tokens();
		$result = $api->get();

		$this->assertNotEmpty($result);
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
	
	public function testAddToken(): void {
		$token_id = rand(100000000, 999999999);
		$api = new Tokens();
		$result = $api->addToken($token_id);
		$this->assertTrue($result);
		$api->removeToken($token_id);
	}
	
	public function testRemoveToken(): void {
		$token_id = rand(100000000, 999999999);
		$api = new Tokens();
		$result = $api->addToken($token_id);
		$this->assertTrue($result);
		
		$result = $api->removeToken($token_id);
		$this->assertTrue($result);
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

    public function tearDown(): void {
	    $api = new Tokens();
	    $api->removeToken($this->token_id);

        $api = new Apps();
        $api->delete(Auth::appId());
    }
}
