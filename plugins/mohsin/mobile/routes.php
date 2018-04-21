<?php

Route::group(['prefix' => 'api/v1'], function () {
    Route::resource('installs', 'Mohsin\Mobile\Http\Installs');
});
