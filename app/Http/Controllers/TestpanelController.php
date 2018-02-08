<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestpanelController extends Controller {
	public function __construct() {

	}

	public function index() {
		return view('test-panel');
	}

	public function createIdentity(string $identity) {
		$identity = explode(',', $identity);
		$newIdentity = [];
		foreach ($identity as $id) {
			$field = explode('|', trim($id));
			if (!empty($field[0]) && !empty($field[1]) && !in_array($field[0], ['identity_code', 'identity_id'])) {
				$newIdentity[$field[0]] = $field[1];
			}
		}

		return $this->identities->create($newIdentity);
	}

	public function linkIdentity(string $identityCode, string $ethereumAddress, string $signature = null) {
		if (empty($signature)) {
			$signature = null;
		}

		return $this->identities->link($identityCode, $ethereumAddress, $signature);
	}

	public function deleteIdentity(string $identityCode) {
		return $this->identities->delete(['identity_code' => $identityCode]);
	}

	public function updateIdentity(string $identityCode, string $identity) {
		$identity = explode(',', $identity);
		$newIdentity = [];
		foreach ($identity as $id) {
			$field = explode('|', trim($id));
			if (!empty($field[0]) && !empty($field[1]) && !in_array($field[0], ['identity_code', 'identity_id'])) {
				$newIdentity[$field[0]] = $field[1];
			}
		}

		return $this->identities->update(['identity_code' => $identityCode], $newIdentity, true);
	}
}
