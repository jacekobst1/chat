<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

Auth::routes(['register' => false, 'reset' => false]);

Route::group(['auth'], function() {
    Route::get('/',                     'MessageController@index');
    Route::get('/getNewMessages',       'MessageController@getNew');
    Route::post('/storeMessage',        'MessageController@store');
});

Route::get('/reset', function() {
    Cache::flush();
    Artisan::call('migrate:fresh');
    return redirect('/');
});
