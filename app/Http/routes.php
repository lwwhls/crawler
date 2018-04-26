<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 所有部门
Route::get('test', 'CctvController@test')->name('groups.index');

Route::group(['prefix'=>'api','namespace'=>'Api'],function(){
    Route::get('article/index', 'ApiArticleController@index')->name('api.article.index');
});


