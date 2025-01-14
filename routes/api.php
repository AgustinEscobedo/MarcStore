<?php

use App\Http\Controllers\ProductosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware'=>['auth:sanctum']],function(){
    Route::get('user-profile',[AuthController::class,'userProfile']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout',[AuthController::class,'logout']);
    Route::get('getAllProductos',[ProductosController::class,'getAllProductos']);
    Route::post('updateProducto',[ProductosController::class,'updateProducto']);
    Route::post('deleteProducto',[ProductosController::class,'deleteProducto']);
    Route::post('newProducto',[ProductosController::class,'newProducto']);
    Route::post('deleteUser',[AuthController::class,'deleteUser']);
    Route::post('updateUser',[AuthController::class,'updateUser']);
});

// Route::get('/userProfile', [AuthController::class, 'userProfile']);
// Route::get('/allUsers', [AuthController::class, 'allUsers']);
