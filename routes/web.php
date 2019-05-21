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

Route::get('/', function () {
    return view('welcome');
});
//测试
Route::any('/Text/test','Test\TextController@test');
Route::get('/info', function () {
    phpinfo();
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth']], function () {
    //注册
    Route::get('/regist','Api\ApiController@regist');
    Route::post('/registDo','Api\ApiController@registDo');
    //查询订单状态
    Route::get('/get_status/{id}','Api\ApiController@get_status');
    //审核通过
    Route::get('/access_token/{id}','Api\ApiController@access_token');
});
    //获取token
    Route::get('/get_access','Api\ApiController@get_access');

Route::group(['middleware' => ['checkToken']], function () {
    //获取客户端ip
    Route::get('/get_kip','Api\ApiController@get_kip');
    //获取客户端ua
    Route::get('/get_kua','Api\ApiController@get_kua');
    //个人信息
    Route::get('/get_userinfo','Api\ApiController@get_userinfo');

});