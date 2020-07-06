<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('v1/login', 'Api\AuthController@login');

Route::group(['prefix' => '/v1', 'middleware' => ['auth:api'], 'guard' => 'api'], function () {

	Route::any('sms/send', 'Api\SmsController@send');

});