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

Route::get('/', function (\Illuminate\Http\Request $request) {
    // return view('welcome');
    $user = $request->user();
    $user->togglePermission(['edit posts', 'delete posts', 'delete users']);
    return new \Illuminate\Http\Response('hello', 200);
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'role:admin'], function () {
    
    Route::group(['middleware' => 'role:admin,delete users'], function () {
            Route::get('/admin/users', function () {
                return 'Delete users in admin panel';
        });
    });

    Route::get('/admin', function () {
        return 'Admin panel';
    });
});

