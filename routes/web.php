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
Route::get('login',function (){
    return redirect('/admin');
});

Route::get('/test', function () {
    \Illuminate\Support\Facades\Auth::guard()->loginUsingId(1);
});

Route::get('/test1', function (\Illuminate\Http\Request $request) {
    var_dump($request->user()->name);
});


Route::group(['namespace' => 'Admin'], function () {

    Route::get('/admin', 'AmsController@index');
    Route::get('/admin/login', 'AmsController@login');
    Route::get('/admin/callback', 'AmsController@callback');
    Route::get('/admin/logout', 'AmsController@logout');

    Route::get('index', ['as' => 'admin.index', 'uses' => function () {
        return redirect('/home');
    }]);

    Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'menu']], function () {
        //权限管理路由
        Route::get('permission/{cid}/create', ['as' => 'admin.permission.create', 'uses' => 'PermissionController@create']);
        Route::get('permission/manage', ['as' => 'admin.permission.manage', 'uses' => 'PermissionController@index']);
        Route::get('permission/{cid?}', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']);
        Route::post('permission/index', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']); //查询
        Route::resource('permission', 'PermissionController', ['names' => ['update' => 'admin.permission.edit', 'store' => 'admin.permission.create']]);

    });

    Route::get('/home','HomeController@index');

    Route::group(['prefix'=>'m3gcn'],function(){
        Route::get('/','M3gcnController@index');

    });

    Route::group(['prefix'=>'m3ghk'],function(){
        Route::get('/','M3ghkController@index');

    });

    Route::group(['prefix'=>'m3gvn'],function(){
        Route::get('/','M3gcnController@index');

    });
//
    Route::get('/module','ModuleController@index');
    Route::get('/addPageAction','ModuleController@addPageAction');
    Route::get('/addOrUpdateModuleView','ModuleController@addOrUpdateModuleView');
    Route::get('/addOrupdateModuleAction','ModuleController@addOrupdateModuleAction');
    Route::get('/addOrUpdateSqlWhere','ModuleController@addOrUpdateSqlWhere');
    Route::get('/publish','ModuleController@publish');
    Route::get('/delete','ModuleController@delete');
    Route::get('/copy','ModuleController@copy');
    Route::get('/recover','ModuleController@recover');
    Route::get('/getModuleSql','ModuleController@getModuleSql');




});