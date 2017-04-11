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
});//->middleware('auth:api');

Route::group(['prefix' => 'twitter'], function () {
    Route::any('/test', 'MainHashtagController@test');
    Route::any('/streaming', 'MainHashtagController@streaming');
    Route::any('/search', 'MainHashtagController@search');
    Route::any('/filter', 'MainHashtagController@filter');
    Route::get('/export', 'MainHashtagController@export');
    Route::any('/import', 'MainHashtagController@import');
});
