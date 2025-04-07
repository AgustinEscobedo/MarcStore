<?php

use App\Http\Controllers\EmailController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\RegistroController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ServicioController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);

Route::post('/enviarCorreo', [EmailController::class, 'sendEmail']);

Route::post('register', [AuthController::class, 'register']);

Route::get('/getAllServicios', [ServicioController::class, 'index']);
Route::post('/registerServicios', [ServicioController::class, 'store']);
Route::post('/deleteServicios', [ServicioController::class, 'destroy']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    //USUARIO
    // Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::get('logout', [AuthController::class, 'logout']);
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('deleteUser', [AuthController::class, 'deleteUser']);
    Route::post('updateUser', [AuthController::class, 'updateUser']);
    Route::post('suspendUsers', [AuthController::class, 'suspendUsers']);
    Route::get('getAllUsers', [AuthController::class, 'getAllUsers']);

    //PRODUCTOS
    Route::get('getAllProductos', [ProductosController::class, 'getAllProductos']);
    Route::post('updateProducto', [ProductosController::class, 'updateProducto']);
    Route::post('deleteProducto', [ProductosController::class, 'deleteProducto']);
    Route::post('newProducto', [ProductosController::class, 'newProducto']);

    //VENTAS
    Route::post('registrarVenta', [VentasController::class, 'registrarVenta']);
    Route::post('editarVenta', [VentasController::class, 'editarVenta']);
    Route::post('eliminarVenta', [VentasController::class, 'eliminarVenta']);
    Route::post('getAllVentas', [VentasController::class, 'getAllVentas']);
    Route::post('obtenerVentasConDetalles', [VentasController::class, 'obtenerVentasConDetalles']);

    //PROVEEDORES
    Route::post('newProveedor', [ProveedorController::class, 'newProveedor']);
    Route::post('deleteProveedor', [ProveedorController::class, 'deleteProveedor']);
    Route::post('updateProveedor', [ProveedorController::class, 'updateProveedor']);
    Route::get('getAllProveedores', [ProveedorController::class, 'getAllProveedores']);

    //REGISTROS
    Route::post('registrarEntradaSalida', [RegistroController::class, 'registrarEntradaSalida']);
    Route::post('registrosUsuario', [RegistroController::class, 'obtenerRegistrosUsuario']);
    Route::post('actualizarHoraRegistro', [RegistroController::class, 'actualizarHoraRegistro']);
    Route::post('obtenerRegistrosPorFechaUsuario', [RegistroController::class, 'obtenerRegistrosPorFechaUsuario']);
    Route::post('getAllRegistros', [RegistroController::class, 'obtenerTodosLosRegistros']);

});

// Route::get('/userProfile', [AuthController::class, 'userProfile']);
// Route::get('/allUsers', [AuthController::class, 'allUsers']);
