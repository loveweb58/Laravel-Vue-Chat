<?php
Route::group([
    'middleware' => ['api'],
    'namespace'  => '\App\MyPhone\Sms\Http\Controllers',
    'prefix'     => 'sms',
], function () {
    Route::any('{provider}/receive', 'SmsController@receive')->where('provider', 'aerialink');
    Route::any('{provider}/delivery', 'SmsController@delivery')->where('provider', 'aerialink');
});