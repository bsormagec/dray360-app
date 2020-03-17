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



// testing roles and permissions
//Route::get('/', function () {
//    return view('welcome');
//});
Auth::routes();
Route::get('/admin/dashboard', function() {
    return 'Welcome Admin!';
})->name('admin.dashboard');
Route::get('/home', 'VueController@index')->name('home');


// Default VUECONTROLLER route
Route::get('/{any}', 'VueController@index')->where('any', '.*');


