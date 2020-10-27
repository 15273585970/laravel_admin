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

//追加文件上传
Route::get('appendUploadFile','FileController@appendUploadFile');
Route::get('appendSploadString','FileController@appendSploadString');



//分片上传
Route::get('ShardToUpload','FileController@ShardToUpload');
Route::get('ShardToUploadLocal','FileController@ShardToUploadLocal');  //本地分片上传
Route::get('ShardToUpload','FileController@ShardToUpload');
Route::get('ShardToUploadDir','FileController@ShardToUploadDir');
Route::get('getAlreadyShardUpload','FileController@getAlreadyShardUpload');


//下载文件到本地
Route::get('dwLocalFile','FileController@dwLocalFile');

Route::any('file_upload','FileController@upload');


