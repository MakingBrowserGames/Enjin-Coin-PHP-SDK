<?php
declare(strict_types=1);

namespace EnjinCoin\Api\Test;

use EnjinCoin\Api\Tokens;
use EnjinCoin\Api\Apps;
use EnjinCoin\Auth;
use EnjinCoin\Test\BaseTest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;

/**
 * @covers EnjinCoin\Api\Tokens
 */
final class TokensTest extends BaseTest {

	protected $token_id = 0;
	protected $app_id = '';
	protected $app_auth_key = '';
	protected $app_name = '';
	protected $appsApi;
	protected $tokensApi;

	//Setup method called before every method 
	protected function setUp(): void {
		parent::setUp();
		$this->appsApi = new Apps();
		$this->app_name = 'TestApp_' . rand(1, 999999999);

		$result = $this->appsApi->create($this->app_name);
		$this->app_id = $result['app_id'];
		$this->app_auth_key = $result['app_auth_key'];
		Auth::init($this->app_auth_key);

		//Add a token before each test
		$this->token_id = rand(100000000, 999999999);
		$this->tokensApi = new Tokens();
		$result = $this->tokensApi->addToken($this->token_id);
		$this->assertTrue($result);
	}

	public function testGet_AppIdSet(): void {
		$result = $this->tokensApi->get($this->app_id);

		$this->assertNotEmpty($result);
		$this->assertNotEmpty($result[0]);
		$this->assertNotEmpty($result[0]['token_id']);
		$this->assertNotEmpty($result[0]['app_id']);
		$this->assertSame((int) $result[0]['app_id'], (int) $this->app_id);
	}

	public function testGet_AuthHasOccuredNoTokens(): void {
		$result = Auth::init($this->app_auth_key);
		$this->assertTrue($result);

		$result = $this->tokensApi->get();
		$this->assertNotEmpty($result);
		$this->assertNotEmpty($result[0]);
		$this->assertNotEmpty($result[0]['token_id']);
		$this->assertNotEmpty($result[0]['app_id']);
		$this->assertSame((int) $result[0]['app_id'], (int) $this->app_id);
	}

	public function testGet_AppIdNotSet(): void {
		$result = $this->tokensApi->get();

		$this->assertNotEmpty($result);
	}

	public function testGet_AfterTokenIdSet(): void {
		$after_token_id = 1;
		$result = $this->tokensApi->get($this->app_id, $after_token_id);

		$this->assertNotEmpty($result);
		$this->assertNotEmpty($result[0]);
		$this->assertNotEmpty($result[0]['token_id']);
		$this->assertNotEmpty($result[0]['app_id']);
		$this->assertSame((int) $result[0]['app_id'], (int) $this->app_id);
	}

	public function testGet_LimitSet(): void {
		$after_token_id = 1;
		$limit = 100;
		$result = $this->tokensApi->get($this->app_id, $after_token_id, $limit);

		$this->assertNotEmpty($result);
		$this->assertNotEmpty($result[0]);
		$this->assertNotEmpty($result[0]['token_id']);
		$this->assertNotEmpty($result[0]['app_id']);
		$this->assertSame((int) $result[0]['app_id'], (int) $this->app_id);
	}

	public function testGet_TokenIdSet(): void {
		$after_token_id = 1;
		$limit = 100;
		$token_id = 1;
		$result = $this->tokensApi->get($this->app_id, $after_token_id, $limit, $token_id);

		$this->assertEmpty($result);
	}

	public function testAddToken(): void {
		$token_id = rand(100000000, 999999999);
		$result = $this->tokensApi->addToken($token_id);
		$this->assertTrue($result);
		$this->tokensApi->removeToken($token_id);
	}

	public function testRemoveToken(): void {
		$token_id = rand(100000000, 999999999);
		$result = $this->tokensApi->addToken($token_id);
		$this->assertTrue($result);

		$result = $this->tokensApi->removeToken($token_id);
		$this->assertTrue($result);
	}

	public function testGetBalance_IdentityArrayEmpty(): void {
		$result = $this->tokensApi->getBalance([]);

		$this->assertEmpty($result);
	}

	public function testGetBalance_IdentityNotEmptyIdIs0(): void {
		$this->identity_id = 0;
		$result = $this->tokensApi->getBalance(['identity_id' => $this->identity_id]);

		$this->assertEmpty($result);
	}

	public function testGetBalance_IdentityNotEmptyIdIs1(): void {
		$this->identity_id = 1;
		$result = $this->tokensApi->getBalance(['identity_id' => $this->identity_id]);

		$this->assertNotEmpty($result);
		$this->assertArrayHasKey('ENJ', $result);
		$this->assertArrayHasKey('1', $result);
		$this->assertArrayHasKey('2', $result);
		$this->assertArrayHasKey('3', $result);
		$this->assertArrayHasKey('4', $result);
	}

	public function tearDown(): void {
		$this->tokensApi->removeToken($this->token_id);

		$this->appsApi->delete(Auth::appId());
	}
}
