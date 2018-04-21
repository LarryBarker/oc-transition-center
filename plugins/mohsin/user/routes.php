<?php

Route::group(['prefix' => 'api/v1/account'], function () {
    Route::post('signin', 'Mohsin\User\Http\Account@signin');
    Route::post('register', 'Mohsin\User\Http\Account@register');
});
