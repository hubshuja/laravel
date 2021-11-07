<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [App\Http\Controllers\API\Auth\LoginController::class,'login']);

Route::apiResource('register', App\Http\Controllers\API\Auth\RegisterController::class);

Route::get('create-user-invite', [App\Http\Controllers\API\Auth\RegisterController::class,'saveInviteUser']);

Route::get('ativate-account', [App\Http\Controllers\API\Auth\RegisterController::class,'activateAccount']);



 Route::group(['middleware' => ['auth:api', 'user_accessible']], function () {
      
     
     Route::post('share-link', [App\Http\Controllers\API\UserController::class, 'shareLink']);
    

    });