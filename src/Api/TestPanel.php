<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use EnjinCoin\Auth;
use EnjinCoin\Util\Db;
use EnjinCoin\Api\Identities;
use EnjinCoin\Api\Tokens;
use EnjinCoin\Api\Events;

/**
 * Class TestPanel
 * @package EnjinCoin\Api
 */
class TestPanel extends ApiBase {
	public function __construct() {
		parent::__construct();
		$this->identities = new Identities();
		$this->tokens = new Tokens;
		$this->events = new Events;
	}

	/*
	 * Identity Methods
	 */

	public function createIdentity(string $playerName) {
		return $this->identities->create(['player_name' => $playerName]);
	}

	public function linkIdentity(string $identityCode, string $ethereumAddress) {
		return $this->identities->link($identityCode, $ethereumAddress);
	}

	public function deleteIdentity(string $identityCode) {
		return $this->identities->delete(['identity_code' => $identityCode]);
	}

	public function updateIdentity(string $identityCode, string $playerName) {
		return $this->identities->update(['identity_code' => $identityCode], ['player_name' => $playerName], false);
	}

	/*
	 * Token Methods
	 */

	public function createToken() {

	}

	public function updateToken() {

	}

	/*
	 * Event Methods
	 */
}
