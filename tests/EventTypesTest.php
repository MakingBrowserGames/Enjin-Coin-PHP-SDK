<?php


namespace EnjinCoin\Test;

use EnjinCoin\Api\Apps;
use EnjinCoin\Api\Identities;
use EnjinCoin\Api\Tokens;
use EnjinCoin\Api\TransactionRequests;
use EnjinCoin\Auth;
use EnjinCoin\EventTypes;

class EventTypesTest extends BaseTest {

	/**
	 * @var Apps
	 */
	private $apps;
	/**
	 * @var Identities
	 */
	private $identities;
	/**
	 * @var Tokens
	 */
	private $tokens;
	/**
	 * @var TransactionRequests
	 */
	private $transactions;
	/**
	 * @var EventTypes
	 */
	private $events;
	private $app;
	private $identity;
	private $sender;
	private $token;
	private $transaction;

	protected function setUp(): void {
		parent::setUp();

		$this->apps = new Apps();
		$this->identities = new Identities();
		$this->tokens = new Tokens();
		$this->transactions = new TransactionRequests();
		$this->events = new EventTypes();

		$this->app = $this->apps->create('app');
		Auth::init($this->app['app_auth_key']);

		$this->identity = $this->identities->create([
			'player_name' => 'enjin',
			'ethereum_address' => '0x0000000000000000000000000000000000000001'
		]);
		$this->sender = $this->identities->create([
			'player_name' => 'enjin_sender',
			'ethereum_address' => '0x0000000000000000000000000000000000000002'
		]);

		$this->token = 34714;
		$this->tokens->addToken($this->token);

		$this->transaction = $this->transactions->create(
			$this->sender,
			$this->identity,
			TransactionRequests::TYPE_SEND,
			null,
			null,
			$this->token,
			10
		);
	}

	public function testCallEvent_Unknown(): void {
		$result = $this->events->callEvent(
			EventTypes::UNKNOWN_EVENT,
			Auth::appId(),
			$this->identity['identity_id'],
			null
		);

		$this->assertFalse($result);
	}

	public function testCallEvent_TransactionRequestPending(): void {
		$result = $this->events->callEvent(
			EventTypes::TXR_PENDING,
			Auth::appId(),
			$this->identity['identity_id'],
			[
				'txr_id' => $this->transaction,
				'identity' => $this->sender,
				'recipient' => $this->identity,
				'type' => TransactionRequests::TYPE_SEND,
				'icon' => null,
				'title' => null,
				'value' => 10
			]
		);

		$this->assertNotEmpty($result);
		$this->assertEquals('pending', $result['data']['state']);
	}

	public function testCallEvent_TransactionRequestCanceledUser(): void {
		$result = $this->events->callEvent(
			EventTypes::TXR_CANCELED_USER,
			Auth::appId(),
			$this->identity['identity_id'],
			[
				'txr_id' => $this->transaction,
				'identity' => $this->sender
			]
		);

		$this->assertNotEmpty($result);
		$this->assertEquals('canceled_user', $result['data']['state']);
	}

	public function testCallEvent_TransactionRequestCanceledPlatform(): void {
		$result = $this->events->callEvent(
			EventTypes::TXR_CANCELED_PLATFORM,
			Auth::appId(),
			$this->identity['identity_id'],
			[
				'txr_id' => $this->transaction,
				'identity' => $this->sender
			]
		);

		$this->assertNotEmpty($result);
		$this->assertEquals('canceled_platform', $result['data']['state']);
	}

	public function testCallEvent_TransactionRequestAccepted(): void {
		$result = $this->events->callEvent(
			EventTypes::TXR_ACCEPTED,
			Auth::appId(),
			$this->identity['identity_id'],
			[
				'txr_id' => $this->transaction,
				'tx_id' => '0x0000000000000000000000000000000000000000',
				'identity' => $this->sender,
				'recipient' => $this->identity,
				'type' => TransactionRequests::TYPE_SEND,
				'icon' => null,
				'title' => null,
				'value' => 10
			]
		);

		$this->assertNotEmpty($result);
		$this->assertEquals('accepted', $result['data']['state']);
	}

	public function testCallEvent_TransactionBroadcasted(): void {
		$result = $this->events->callEvent(
			EventTypes::TX_BROADCASTED,
			Auth::appId(),
			$this->identity['identity_id'],
			[
				'txr_id' => $this->transaction,
				'tx_id' => '0x0000000000000000000000000000000000000000',
			]
		);

		$this->assertNotEmpty($result);
		$this->assertEquals(0, $result['data']['confirmations']);
	}

	public function testCallEvent_TransactionExecuted(): void {
		$result = $this->events->callEvent(
			EventTypes::TX_EXECUTED,
			Auth::appId(),
			$this->identity['identity_id'],
			[
				'tx_id' => '0x0000000000000000000000000000000000000000',
				'confirmations' => 1
			]
		);

		$this->assertNotEmpty($result);
		$this->assertEquals(1, $result['data']['confirmations']);
	}

	public function testCallEvent_TransactionConfirmed(): void {
		$result = $this->events->callEvent(
			EventTypes::TX_CONFIRMED,
			Auth::appId(),
			$this->identity['identity_id'],
			[
				'tx_id' => '0x0000000000000000000000000000000000000000',
				'confirmations' => 5
			]
		);

		$this->assertNotEmpty($result);
		$this->assertEquals(5, $result['data']['confirmations']);
	}

	public function testCallEvent_IdentityLinked(): void {
		$result = $this->events->callEvent(
			EventTypes::IDENTITY_LINKED,
			Auth::appId(),
			$this->identity['identity_id'],
			['identity' => $this->identity]
		);

		$this->assertNotEmpty($result);
	}

	public function testCallEvent_IdentityCreated(): void {
		$result = $this->events->callEvent(
			EventTypes::IDENTITY_CREATED,
			Auth::appId(),
			$this->identity['identity_id'],
			['identity' => $this->identity, 'identity_code' => $this->identity['identity_code']]
		);

		$this->assertNotEmpty($result);
	}

	public function testCallEvent_IdentityUpdated(): void {
		$result = $this->events->callEvent(
			EventTypes::IDENTITY_UPDATED,
			Auth::appId(),
			$this->identity['identity_id'],
			['identity' => $this->identity]
		);

		$this->assertNotEmpty($result);
	}

	public function testCallEvent_IdentityDeleted(): void {
		$result = $this->events->callEvent(
			EventTypes::IDENTITY_DELETED,
			Auth::appId(),
			$this->identity['identity_id'],
			['identity' => $this->identity]
		);

		$this->assertNotEmpty($result);
	}

	public function testCallEvent_BalanceUpdated(): void {
		$result = $this->events->callEvent(
			EventTypes::BALANCE_UPDATED,
			Auth::appId(),
			$this->identity['identity_id'],
			['identity' => $this->identity, 'from' => $this->sender, 'pending' => true, 'confirmed' => false]
		);

		$this->assertNotEmpty($result);
	}

	public function testCallEvent_BalanceMelted(): void {
		$result = $this->events->callEvent(
			EventTypes::BALANCE_MELTED,
			Auth::appId(),
			$this->identity['identity_id'],
			['identity' => $this->identity, 'from' => $this->sender, 'pending' => true, 'confirmed' => false, 'enj' => 10]
		);

		$this->assertNotEmpty($result);
	}

	public function testCallEvent_TokenCreated(): void {
		$result = $this->events->callEvent(
			EventTypes::TOKEN_CREATED,
			Auth::appId(),
			$this->identity['identity_id'],
			['token_id' => $this->token]
		);

		$this->assertNotEmpty($result);
	}

	public function testCallEvent_TokenUpdated(): void {
		$result = $this->events->callEvent(
			EventTypes::TOKEN_UPDATED,
			Auth::appId(),
			$this->identity['identity_id'],
			['token_id' => $this->token]
		);

		$this->assertNotEmpty($result);
	}

}