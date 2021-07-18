<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\productsController;
use \App\Http\Controllers\Api\cartController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});

Route::resources([
    'products' => productsController::class,
    'cart' => cartController::class
]);

Route::post('cart/add',[CartController::class,'add']);
Route::post('cart/update',[CartController::class,'update']);
Route::post('cart/delete',[CartController::class,'destroy']);
Route::post('cart/submit', [CartController::class, 'submit']);
