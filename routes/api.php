<?php

use Illuminate\Http\Request;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/ugc/v1/add', 'UgcController@add');
Route::get('/ugc/v1/get/{id}', 'UgcController@get')->where('id', '[0-9]+');
Route::post('/ugc/v1/edit/{id}', 'UgcController@edit')->where('id', '[0-9]+');
Route::get('/ugc/v1/delete/{id}', 'UgcController@delete')->where('id', '[0-9]+');
Route::get('/ugc/v1/getbycreator/{creator}', 'UgcController@getByCreator')->where('creator', '[0-9]+');
Route::get('/ugc/v1/getbyparent/{reply_to}', 'UgcController@getByReplyTo')->where('reply_to', '[0-9]+');