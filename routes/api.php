<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Producto\ColorController;
use App\Http\Controllers\Producto\TallaController;
use App\Http\Controllers\Producto\CategoriaController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});
Route::post('/auth/register', [RegisterController::class, 'registerUser']);
Route::post('/auth/login', [LoginController::class, 'loginUser']);
Route::middleware('auth:sanctum')->get('/auth/logout', [LogoutController::class, 'logoutUser']);

Route::middleware('auth:sanctum')->prefix('admin/colores')->group(function () {
    Route::get('/', [ColorController::class, 'index']); // Listar todos los colores
    Route::post('/', [ColorController::class, 'store']); // Crear un nuevo color
    Route::get('{id}', [ColorController::class, 'show']); // Obtener un color específico
    Route::put('{id}', [ColorController::class, 'update']); // Actualizar un color
    Route::delete('{id}', [ColorController::class, 'destroy']); // Eliminar un color
});

Route::middleware('auth:sanctum')->prefix('admin/tallas')->group(function () {
    Route::get('/', [TallaController::class, 'index']); // Listar todas las tallas
    Route::post('/', [TallaController::class, 'store']); // Crear nueva talla
    Route::get('{id}', [TallaController::class, 'show']); // Mostrar una talla específica
    Route::put('{id}', [TallaController::class, 'update']); // Actualizar talla
    Route::delete('{id}', [TallaController::class, 'destroy']); // Eliminar talla
});

Route::middleware('auth:sanctum')->prefix('admin/categorias')->group(function () {
    Route::get('/', [CategoriaController::class, 'index']); // Listar todas las categorías
    Route::post('/', [CategoriaController::class, 'store']); // Crear nueva categoría
    Route::get('{id}', [CategoriaController::class, 'show']); // Mostrar una categoría específica
    Route::put('{id}', [CategoriaController::class, 'update']); // Actualizar categoría
    Route::delete('{id}', [CategoriaController::class, 'destroy']); // Eliminar categoría
});