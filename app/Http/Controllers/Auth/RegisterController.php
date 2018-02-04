<?php

namespace App\Http\Controllers\Auth;

use App\Identity;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:identities',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new identity instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Identity
     */
    protected function create(array $data)
    {
        // Create the new identity.
        $identity = Identity::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // If an ethereum address was supplied at the same time then link it to the identity,
        // otherwise create a linking code so it can be done later.
        if(isset($data['ethereum_address'])) {
            $identity->enjinWallet()->create(['ethereum_address' => $data['ethereum_address']]);
        }
        else {
            $identity->enjinWallet()->create(['linking_code' => $identity->generateLinkingCode()]);
        }

        // Create a Bearer Token
        $identity->createToken('EnjinCoin Token')->accessToken;

        // Return the identity.
        return $identity;
    }
}
