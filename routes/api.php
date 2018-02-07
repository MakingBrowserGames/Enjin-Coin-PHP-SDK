<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Identity API Routes
|--------------------------------------------------------------------------
|
| Endpoints for dealing with identities and wallets.
|
*/

// Open endpoints for login and linking wallets where no oAuth Bearer Token is needed.
Route::get('/v1/user/login', 'UserController@login');
Route::put('/v1/identity/link-wallet/{linkingCode}', 'IdentityController@updateWallet');


Route::resource('/v1/users', 'UserController');

// RESTful resource routes for identity collections.
// A Bearer Token is required to access most of these endpoints,
// other than POST requests to /v1/identities which is open to enable creating new identities.
Route::resource('/v1/identities', 'IdentityController');

// A singe GET end point to retrieve the current user from their Bearer Token.
Route::middleware('auth:api')->get('/v1/identity', function (Request $request) {
    $user = $request->user();
    $user->enjinWallet;
    return $user;
});
