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

//Route::get('test', 'HomeController@test');
//Route::get('/test', [App\Http\Controllers\HomeController::class, 'test'])->name('test');

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('test', [App\Http\Controllers\API\RoleController::class,'test'])->name('test');