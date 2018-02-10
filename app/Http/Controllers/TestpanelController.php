<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EnjinCoin\EnjinIdentity;
use EnjinCoin\EnjinIdentityField;
use Auth;

class TestpanelController extends Controller {
	public function __construct() {

	}

	public function index() {
		return view('test-panel');
	}

	public function createIdentity(Request $request) {
		$identity = explode(',', $request->input('identity'));
		$newIdentity = [];
		foreach ($identity as $id) {
			$field = explode('|', trim($id));
			if (!empty($field[0]) && !empty($field[1]) && !in_array($field[0], ['identity_code', 'identity_id'])) {
				$newIdentity[$field[0]] = $field[1];
			}
		}

		// Create a new Identity Model
		$identity = new EnjinIdentity();
		$identity->user_id = Auth::id();
		$identity->linking_code = $identity->generateLinkingCode();

		// Save the new identity
		$identity->save();

		// If identity fields have been supplied then add them.
		if (!empty($newIdentity)) {
			foreach ($newIdentity as $key => $value) {
				// Check for keys which aren't allowed.
				if (in_array(strtolower($key), ['identity_id', 'identity_code', 'ethereum_address'])) {
					continue;
				}

				// Search to see if the current key is already in the database.
				$field = EnjinIdentityField::where('key', $key)->first();

				// If not then create it.
				if (!isset($field)) {
					$field = new EnjinIdentityField();
					$field->key = $key;
					$field->save();
				}

				// Attach to the identity
				$identity->fields()->attach($field, ['field_value' => $value[$key]]);
			}
		}

		return response()->json($identity);
	}

	public function linkIdentity(Request $request) {

	}

	public function deleteIdentity(Request $request) {

	}

	public function updateIdentity(Request $request) {

	}
}
