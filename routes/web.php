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

//Route::get('/', function () {
//    return view('welcome');
//});



// 后台登录
Route::prefix('admin')->namespace('Admin')->group(function () {
    //后台首页
    Route::get('/', '\App\Http\Controllers\Admin\LoginController@showLoginForm');
    Route::get('/login', '\App\Http\Controllers\Admin\LoginController@showLoginForm');
    Route::post('/login', '\App\Http\Controllers\Admin\LoginController@login');
    Route::get('/logout', '\App\Http\Controllers\Admin\LoginController@logout')->middleware('config');
});


// 后台控制器
Route::group(['prefix'=>'admin', 'middleware'=>'config'], function (){

    // 首页控制器
    Route::get('/index', '\App\Http\Controllers\Admin\IndexController@index');

    // 菜单控制器
    Route::get('/menu/{position}', '\App\Http\Controllers\Admin\MenuController@index');
    Route::match(['get', 'post'], '/menu/edit/{id}', '\App\Http\Controllers\Admin\MenuController@edit');
    Route::get('/menu/del/{id}', '\App\Http\Controllers\Admin\MenuController@del');

    // 网盘控制器
    Route::group(['prefix'=>'disk'], function (){
        Route::get('/', '\App\Http\Controllers\Admin\DiskController@index');
        Route::match(['get', 'post'], '/edit/{id}', '\App\Http\Controllers\Admin\DiskController@edit');
        Route::get('/del/{id}', '\App\Http\Controllers\Admin\DiskController@del');
        Route::get('/file_list/{id}/{path?}', '\App\Http\Controllers\Admin\DiskController@file_list');
        Route::post('/cache', '\App\Http\Controllers\Admin\DiskController@cache');
    });

    // 权限控制器
    Route::group(['prefix'=>'author'], function (){
        Route::get('/index', '\App\Http\Controllers\Admin\AuthorController@index');
        Route::match(['get', 'post'], '/edit/{id}', '\App\Http\Controllers\Admin\AuthorController@edit');
        Route::get('/del/{id}', '\App\Http\Controllers\Admin\AuthorController@del');
    });


    // 系统设置控制器
    Route::group(['prefix'=>'system'], function (){
        Route::get('/panel', '\App\Http\Controllers\Admin\SystemController@panel');
        Route::match(['get', 'post'], '/setting', '\App\Http\Controllers\Admin\SystemController@setting');
        Route::match(['get', 'post'], '/front', '\App\Http\Controllers\Admin\SystemController@front');
        Route::post('/upload', '\App\Http\Controllers\Admin\SystemController@upload');
    });

});



// 前台控制器
Route::group(['prefix'=>'/', 'middleware'=>'config'], function (){
    // 前台首页控制器
    Route::get('/', '\App\Http\Controllers\Front\IndexController@index');

    // 网盘前台控制器
    Route::group(['prefix'=>'disk'], function (){
        Route::get('/{disk_id}/', '\App\Http\Controllers\Front\DiskController@index');
        Route::get('/thumbnails/{disk_id}/{file_id}', '\App\Http\Controllers\Front\DiskController@thumbnails');
        Route::get('/down_file/{disk_id}/{file_id}', '\App\Http\Controllers\Front\DiskController@downFile');
        Route::get('/download_info/{disk_id}/{file_id}', '\App\Http\Controllers\Front\DiskController@downloadInfo');
        Route::get('/pop_video/{disk_id}/{file_id}', '\App\Http\Controllers\Front\DiskController@popVideo');
        Route::get('/video/{disk_id}/{file_id}', '\App\Http\Controllers\Front\DiskController@video');
        Route::post('/approve', '\App\Http\Controllers\Front\DiskController@approve');
    });
});