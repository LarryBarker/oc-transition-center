<?php



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/





Route::get('/mail/signin', ['uses' => 'Wwrf\Mail\Controllers\AuthController@signin']);

Route::get('/mail/authorize', ['uses' => 'Wwrf\Mail\Controllers\AuthController@gettoken']);

//Route::get('/mail', array('uses' => 'Wwrf\Mail\Controllers\OutlookController@mail', 'as' => 'mail'));