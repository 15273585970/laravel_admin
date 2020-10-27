<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/',function(){
   return view('welcome');
});


Route::any('createBucket','FileController@createBucket');
Route::any('getBucketList','FileController@getBucketList');
Route::any('getBucketDetail','FileController@getBucketDetail');


//设置请求付费者模式
Route::any('setPaymentMode','FileController@setPaymentMode');
Route::any('getPaymentConfig','FileController@getPaymentConfig');
Route::any('getThirdParty','FileController@getThirdParty');


//设置防盗链
Route::any('setPreventingHotlinking','FileController@setPreventingHotlinking');







Route::any('file_upload','FileController@upload');


