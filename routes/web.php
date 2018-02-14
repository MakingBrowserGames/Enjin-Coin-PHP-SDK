<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes for the web facing controllers/views.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/test-panel', 'TestpanelController@index');

// Ethereum routes
Route::get('/ethereum/protocol-version', function() {
    return response()->json(\EnjinCoin\Facades\Ethereum::msg('eth_protocolVersion'));
});

Route::get('/ethereum/{id}/get-balance', function($id){
    return response()->json(\EnjinCoin\Facades\Ethereum::getBalances([$id]));
});
Route::get('/ethereum/{id}/transaction-count', function($id){
    return response()->json(\EnjinCoin\Facades\Ethereum::getTransactionCount($id));
});