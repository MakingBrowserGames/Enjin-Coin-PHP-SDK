<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Apps;
use EnjinCoin\Api\TransactionRequests;
use EnjinCoin\Api\Identities;
use EnjinCoin\Auth;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;
/**
 * @covers EnjinCoin\Api\TransactionRequests
 */	
final class TransactionRequestsTest extends TestCase {

    protected $app_auth_key = '';
	protected $type = '';
	protected $identity;
	protected $validRecipient;
	protected $invalidRecipient;
	protected $latest_txr_id = 0;
	protected $appsApi;
	protected $identitiesApi;
	protected $transactionRequestsApi;
	//Setup method called before every method 
	protected function setUp(): void {
		$this->appsApi = new Apps();
	    $result = $this->appsApi->create('TestApp_' . rand(1, 999999999));
	    $this->app_auth_key = $result['app_auth_key'];
	    Auth::init($this->app_auth_key);

		$this->type = TransactionRequests::TYPE_BUY;

		$this->identitiesApi = new Identities();
		$ethereum_address = '0x0000000000000000000000000000000' . rand(100000000, 999999999);
		$player_name = 'testplayer' . rand(100000000, 999999999);
		$player_name2 = 'testplayer2' . rand(100000000, 999999999);
		
		$result = $this->identitiesApi->create([
			'ethereum_address' => $ethereum_address,
			'player_name' => $player_name,
		]);
		$this->identity_id   = $result['identity_id'];
		$this->identity_code = $result['identity_code'];
		$this->identity = [
			'identity_id' => $this->identity_id, 
			'identity_code' => $this->identity_code,
		];
		$this->validRecipient = [
			'identity_id' => $this->identity_id, 
			'identity_code' => $this->identity_code,
		];

		$result = $this->identitiesApi->create([
			'ethereum_address' => '',
			'player_name' => $player_name2,
		]);		
		$this->invalidRecipient = [
			'identity_id' => $result['identity_id'], 
			'identity_code' => $result['identity_code'],
		];	
		
		//Create a transaction request for the get calls
		$this->transactionRequestsApi = new TransactionRequests();	
		$result = $this->transactionRequestsApi->create($this->identity, $this->validRecipient, $this->type);
		$this->assertNotEmpty($result);

		$latestResult = $this->transactionRequestsApi->getLatest(1);
		$this->assertNotEmpty($latestResult);

		$latest_txr_id = (int)$latestResult['txr_id'];
	}
	
	public function testGets(): void {
		//Create a transaction request for the get calls
		$result = $this->transactionRequestsApi->create($this->identity, $this->validRecipient, $this->type);
		$this->assertNotEmpty($result);

		$latestResult = $this->transactionRequestsApi->getLatest(1);
		$this->assertNotEmpty($latestResult);

		$txr_id = (int)$latestResult['txr_id'];
		$result = $this->transactionRequestsApi->get($txr_id);
		$this->assertNotEmpty($result);
	}
	
	
	/**
     * @expectedException Exception
     */
	public function testCreate_TypeIsUnknown(): void {
		$tempType = $this->type . rand(100000000, 999999999);

		$result = $this->transactionRequestsApi->create($this->identity, $this->validRecipient, $tempType);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Transaction Request Type');
	}
	/**
     * @expectedException Exception
     */
	public function testCreate_IdentityDoesntExist(): void {
		$this->identity = ['identity_id' => $this->identity_id . rand(100000000, 999999999)];
		$result = $this->transactionRequestsApi->create($this->identity, $this->validRecipient, $this->type);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Identity does not exist');
	}
	
	/**
     * @expectedException Exception
     */
	public function testCreate_RecipientDoesntExist(): void {
		$this->validRecipient = ['recipient_id' => $this->identity_id . rand(100000000, 999999999)];
		$result = $this->transactionRequestsApi->create($this->identity, $this->validRecipient, $this->type);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Recipient Identity does not exist');
	}
	
	/**
     * @expectedException Exception
     */
	public function testCreate_RecipientExistsButNoEthereumAddress(): void {
		$result = $this->transactionRequestsApi->create($this->identity, $this->invalidRecipient, $this->type);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Transaction Request Value');
	}	
	
	public function testCreate_RecipientExistsValidEthereumAddress(): void {
		$result = $this->transactionRequestsApi->create($this->identity, $this->validRecipient, $this->type);

		$this->assertNotEmpty($result);
	}

	public function tearDown(): void {
	    $this->identitiesApi->delete(['identity_id' => $this->identity_id]);
	    $this->identitiesApi->delete(['identity_id' => $this->invalidRecipient['identity_id']]);

	    $this->appsApi->delete(Auth::appId());
    }
}
