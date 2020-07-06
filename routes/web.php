<?php

Route::get('/', 'DashboardController@index')->middleware(['permission:dashboard.view', 'auth', 'ga']);
Route::get('docs', 'PagesController@docs')->middleware(['permission:docs.view', 'auth', 'ga']);

Route::get('login', 'LoginController@showLoginForm')->name('login');
Route::get('logout', 'LoginController@logout')->name('logout');
Route::post('login', 'LoginController@ajaxLogin');
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::group(['middleware' => ['permission:dashboard.view', 'auth', 'ga']], function () {

    Route::get('dashboard', 'DashboardController@index');
    Route::get('ga/verify', 'GAController@show');
    Route::post('ga/verify', 'GAController@verify');

    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', 'SettingsController@show');
        Route::post('change-sidebar-menu-state', 'SettingsController@changeSidebarMenuState');
        Route::post('change-password', 'SettingsController@changePassword');
        Route::post('ga', 'SettingsController@GASecret');
        Route::put('profile', 'SettingsController@updateProfile');
        Route::delete('ga', 'SettingsController@GASecretRemove');
        Route::get('api-token', 'SettingsController@apiToken')->middleware('permission:docs.view');
        Route::post('api-sms', 'SettingsController@apiSms')->middleware('permission:docs.view');
    });
    Route::group(['prefix' => 'reports'], function () {
        Route::get('/', 'DashboardController@report')->middleware('permission:admin.reports');
    });
    Route::get('contacts/all', 'ContactsController@all');
    Route::get('contacts/{contact}/export', 'ContactsController@export');
    Route::post('contacts/import', 'ContactsController@import');
    Route::delete('contacts', 'ContactsController@delete')->middleware('permission:contacts.delete');

    Route::resource('contacts', 'ContactsController', ['except' => ['create', 'show', 'destroy']]);
    Route::get('schedule/all', 'ScheduleController@all');
    Route::get('schedule/all1', 'ScheduleController@all1');
    Route::delete('schedule', 'ScheduleController@delete')->middleware('permission:schedule.delete');
    Route::resource('schedule', 'ScheduleController', ['except' => ['show','create','destroy']]);
    Route::resource('custom-labels', 'CustomLabelsController', [
        'except'     => ['create', 'show'],
        'parameters' => ['custom-labels' => 'label'],
    ]);
    Route::resource('message-templates', 'MessageTemplatesController', [
        'except'     => ['create', 'show'],
        'parameters' => ['message-templates' => 'template'],
    ]);
    Route::group(['prefix' => 'groups'], function () {
        Route::get('{group}/export', 'GroupsController@export');
        Route::get('{id}/contacts', 'GroupContactsController@data')->middleware('permission:groups.view');
        Route::post('{id}/contacts', 'GroupContactsController@store')->middleware('permission:groups.update');
        Route::delete('{id}/contacts', 'GroupContactsController@destroy')->middleware('permission:groups.update');
        Route::put('{id}/contacts', 'GroupContactsController@update')->middleware('permission:groups.update');
    });
    Route::post('appointments/settings', 'AppointmentsController@settings')
         ->middleware('permission:appointments.create');
    Route::delete('appointments', 'AppointmentsController@delete')->middleware('permission:appointments.delete');
    Route::resource('appointments', 'AppointmentsController', ['except' => ['create', 'show', 'destroy']]);
    Route::delete('auto-reply', 'AutoReplyController@delete')->middleware('permission:auto_reply.delete');
    Route::post('auto-reply/sort', 'AutoReplyController@sort')->middleware('permission:auto_reply.update');
    Route::resource('auto-reply', 'AutoReplyController', [
        'except'     => ['create', 'show'],
        'parameters' => ['auto-reply' => 'reply'],
    ]);
    Route::delete('forwards', 'ForwardingController@delete')->middleware('permission:forwarding.delete');;
    Route::resource('forwards', 'ForwardingController', ['except' => ['create', 'show', 'destroy']]);


    Route::group(['prefix' => 'messages'], function () {
        Route::post('/', 'MessagesController@sendTest')->middleware('permission:messages.send');
        Route::post('send', 'MessagesController@send')->middleware('permission:messages.send');
        Route::post('send1', 'MessagesController@send1')->middleware('permission:messages.send');
        Route::post('groupa', 'MessagesController@groupa')->middleware('permission:messages.send');
        Route::post('edit', 'MessagesController@scheduleEdit')->middleware('permission:schedule.update');
        Route::post('send-group', 'MessagesController@sendGroups')->middleware('permission:messages.send');
        Route::post('chat', 'MessagesController@chat')->middleware('permission:messages.send');
        Route::post('last-messages', 'MessagesController@lastMessages')->middleware('permission:messages.send');
        Route::delete('/', 'MessagesController@archive')->middleware('permission:messages.send');
        Route::get('/', 'MessagesController@messages')->middleware('permission:messages.send');
        Route::post('mark-as-read', 'MessagesController@markAsRead')->middleware('permission:messages.send');

        Route::get('contact/{id}', 'MessagesController@contact')->middleware('permission:contacts.view');
        Route::get('schedule/{id}', 'MessagesController@schedule')->middleware('permission:schedule.view');

        Route::post('contact/{id}', 'MessagesController@contactEdit')->middleware('permission:contacts.update');
        Route::post('schedule/{id}', 'MessagesController@scheduleEdit')->middleware('permission:schedule.update');
        Route::get('conversations', 'MessagesController@conversations')->middleware('permission:messages.send');
        Route::post('conversations', 'MessagesController@createConversation')->middleware('permission:messages.send');
        Route::put('conversations', 'MessagesController@updateConversation')->middleware('permission:messages.send');
        Route::get('logs', 'MessagesController@logs')->middleware('permission:messages.logs');
        Route::post('logs', 'MessagesController@logsData')->middleware('permission:messages.logs');
        Route::delete('logs', 'MessagesController@delete')->middleware('permission:messages.delete');
    });
    Route::get('accounts/logout', 'AccountsController@logout');
    Route::get('accounts/{account}/auth', 'AccountsController@auth')->middleware('permission:accounts.update');
    Route::resource('blacklist', 'BlacklistController', ['only' => ['index', 'destroy', 'store']]);
    Route::resource('accounts', 'AccountsController', ['except' => ['create']]);
    Route::resource('groups', 'GroupsController', ['except' => ['create']]);
    Route::resource('users', 'UsersController', ['except' => ['show']]);
    Route::resource('roles', 'RolesController', ['except' => ['show']]);
});