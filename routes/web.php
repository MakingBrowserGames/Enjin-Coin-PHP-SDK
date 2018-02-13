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

Route::get('/ethereum/protocol-version', 'EthereumController@protocolVersion');