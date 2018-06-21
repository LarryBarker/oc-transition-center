<?php

Route::group(['prefix' => 'api/v1'], function () {
    Route::resource('installs', 'Wwrf\MobilePlugin\Http\Installs');
});

Route::group(['prefix' => 'api/v1/account'], function () {
    Route::post('signin', 'Wwrf\MobilePlugin\Http\Account@signin');
    Route::post('register', 'Wwrf\MobilePlugin\Http\Account@register');
});