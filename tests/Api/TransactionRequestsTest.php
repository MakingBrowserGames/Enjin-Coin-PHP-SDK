<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\TransactionRequests;
use EnjinCoin\Api\Identities;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;
/**
 * @covers EnjinCoin\Api\TransactionRequests
 */	
final class TransactionRequestsTest extends TestCase {
	
	protected $type = '';
	protected $identity;
	protected $validRecipient;
	protected $invalidRecipient;
	
	//Setup method called before every method 
	protected function setUp(): void {

		$this->type = TransactionRequests::TYPE_BUY;

		$identitiesApi = new Identities();
		$ethereum_address = '0x0000000000000000000000000000000' . rand(100000000, 999999999);
		$player_name = 'testplayer' . rand(100000000, 999999999);
		$player_name2 = 'testplayer2' . rand(100000000, 999999999);
		
		$result = $identitiesApi->create([
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

		$result = $identitiesApi->create([
			'ethereum_address' => '',
			'player_name' => $player_name2,
		]);		
		$this->invalidRecipient = [
			'identity_id' => $result['identity_id'], 
			'identity_code' => $result['identity_code'],
		];
	}
	
	/**
     * @expectedException Exception
     */
	public function testCreate_TypeIsUnknown(): void {
		$tempType = $this->type . rand(100000000, 999999999);

		$api = new TransactionRequests();
		$result = $api->create($this->identity, $this->validRecipient, $tempType);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Transaction Request Type');
	}
	/**
     * @expectedException Exception
     */
	public function testCreate_IdentityDoesntExist(): void {
		$api = new TransactionRequests();
		$this->identity = ['identity_id' => $this->identity_id . rand(100000000, 999999999)];
		$result = $api->create($this->identity, $this->validRecipient, $this->type);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Identity does not exist');
	}
	
	/**
     * @expectedException Exception
     */
	public function testCreate_RecipientDoesntExist(): void {
		$api = new TransactionRequests();	
		$this->validRecipient = ['recipient_id' => $this->identity_id . rand(100000000, 999999999)];
		$result = $api->create($this->identity, $this->validRecipient, $this->type);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Recipient Identity does not exist');
	}
	
	/**
     * @expectedException Exception
     */
	public function testCreate_RecipientExistsButNoEthereumAddress(): void {
		$api = new TransactionRequests();	
		$result = $api->create($this->identity, $this->invalidRecipient, $this->type);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid Transaction Request Value');
	}	
	
	public function testCreate_RecipientExistsValidEthereumAddress(): void {
		$api = new TransactionRequests();	
		$result = $api->create($this->identity, $this->validRecipient, $this->type);

		$this->assertNotEmpty($result);
	}	
}
