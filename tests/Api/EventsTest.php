<?php
declare(strict_types=1);

namespace EnjinCoin\Test\Api;

use EnjinCoin\Api\Events;
use EnjinCoin\Api\Apps;
use EnjinCoin\Api\Identities;
use EnjinCoin\Auth;
use EnjinCoin\EventTypes;
use EnjinCoin\Test\BaseTest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;

/**
 * @covers EnjinCoin\Api\Events
 */
final class EventsTest extends BaseTest {

	protected $token_id = 0;
	protected $app_id = '';
	protected $app_name = '';
	protected $app_auth_key = '';
	protected $event_id;
	protected $event_type;
	protected $event_data;
	protected $identity;
	protected $appsApi;
	protected $eventsApi;
	protected $identitiesApi;

	//Setup method called before every method 
	protected function setUp(): void {
		parent::setUp();
		$this->appsApi = new Apps();
		$result = $this->appsApi->create('TestApp_' . rand(1, 999999999));
		$this->app_id = $result['app_id'];
		$this->app_auth_key = $result['app_auth_key'];
		Auth::init($this->app_auth_key);

		$this->event_type = EventTypes::IDENTITY_LINKED;

		//Create an identity so we can guarantee it exists
		$this->identitiesApi = new Identities();
		$ethereum_address = '0x0000000000000000000000000000000' . rand(100000000, 999999999);
		$player_name = 'testplayer' . rand(100000000, 999999999);

		$result = $this->identitiesApi->create([
			'ethereum_address' => $ethereum_address,
			'player_name' => $player_name,
		]);

		$this->identity_id = $result['identity_id'];
		$this->identity_code = $result['identity_code'];
		$this->identity = [
			'identity_id' => $this->identity_id,
			'identity_code' => $this->identity_code,
		];

		$this->event_data = [
			'identity' => $result
		];

		//Create an event for the get calls
		$this->eventsApi = new Events();
		$result = $this->eventsApi->create($this->app_id, $this->event_type, $this->event_data);
		$this->assertEmpty($result);

		$result = $this->eventsApi->get();
		$this->assertNotEmpty($result);
		$this->assertNotEmpty($result[0]['event_id']);
		$this->event_id = $result[0]['event_id'];
	}

	public function testGet_NoParams(): void {
		$result = $this->eventsApi->get();
		$this->assertNotEmpty($result);
	}

	public function testGet_EventIdSet(): void {
		$result = $this->eventsApi->get($this->event_id);

		$this->assertNotEmpty($result);
	}

	public function testGet_AppIdSet(): void {
		$this->event_id = '';
		$result = $this->eventsApi->get($this->event_id, $this->app_id);

		$this->assertNotEmpty($result);
	}

	public function testGet_IdentitiesSet(): void {
		$this->event_id = '';
		$this->app_id = '';

		$result = $this->eventsApi->get($this->event_id, $this->app_id, $this->identity);

		$this->assertNotEmpty($result);
	}

	public function testGet_BeforeIdentityIdSet(): void {
		$before_event_id = 1;
		$result = $this->eventsApi->get('', '', [], $before_event_id);

		$this->assertNotEmpty($result);
	}

	public function testGet_AfterIdentityIdSet(): void {
		$before_event_id = '';
		$after_event_id = 99999999;
		$result = $this->eventsApi->get('', '', [], $before_event_id, $after_event_id);

		$this->assertNotEmpty($result);
	}

	/**
	 * @expectedException Exception
	 */
	public function testCreate_AppDoesntExist(): void {
		$tempAppId = rand(100000000, 999999999);
		$this->event_data = [];
		$result = $this->eventsApi->create($tempAppId, $this->event_type, $this->event_data);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('App ID does not exist');
	}

	/**
	 * @expectedException Exception
	 */
	public function testCreate_IdentityIdDoesntExist(): void {
		$tempIdentityId = rand(100000000, 999999999);
		$this->event_data = ['identity' => ['identity_id' => $tempIdentityId]];
		$result = $this->eventsApi->create($this->app_id, $this->event_type, $this->event_data);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Identity does not exist');
	}

	public function testCreate_IdentityDoesExist(): void {
		$result = $this->eventsApi->create($this->app_id, $this->event_type, $this->event_data);

		$this->assertEmpty($result);
	}

	/**
	 * @expectedException Exception
	 */
	public function testCreate_AppIdIs0IdentityExistsButInvalidEventType(): void {
		$this->event_type = EventTypes::UNKNOWN_EVENT;

		$tempAppId = 0;
		$result = $this->eventsApi->create($tempAppId, $this->event_type, $this->event_data);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid event type');
	}

	public function testPrune(): void {
		$result = $this->eventsApi->prune();

		$this->assertTrue($result);
	}

	public function tearDown(): void {
		if (!empty($this->identity)) {
			$this->identitiesApi->delete(['identity_id' => $this->identity['identity_id']]);
		}

		$this->appsApi->delete(Auth::appId());
	}
}
