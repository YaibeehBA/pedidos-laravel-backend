<?php

use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\UsuarioController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Pedidos\OrdenController;
use App\Http\Controllers\Producto\ColorController;
use App\Http\Controllers\Producto\TallaController;
use App\Http\Controllers\Producto\ProductoController;
use App\Http\Controllers\Reportes\ReportesController;
use App\Http\Controllers\Dashboard\DashbordController;
use App\Http\Controllers\Producto\CategoriaController;
use App\Http\Controllers\Descuento\DescuentoController;
use App\Http\Controllers\Producto\DetalleProductoController;
use App\Http\Controllers\Notificacion\NotificacionController;


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
Route::post('/auth/forgot-password', [LoginController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [LoginController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->get('/auth/logout', [LogoutController::class, 'logoutUser']);
Route::middleware('auth:sanctum')->get('/notificaciones', [NotificacionController::class, 'index']);
Route::middleware('auth:sanctum')->delete('/notificaciones', [NotificacionController::class, 'destroy']);



Route::middleware('auth:sanctum')->prefix('admin/usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'mostrarUsuario']); // Obtener la lista de todos los usuarios
    Route::put('{id}', [UsuarioController::class, 'actualizarUsuario']);  // Actualizar información de un usuario específico por su ID
    Route::delete('{id}', [UsuarioController::class, 'eliminarUsuario']); // Eliminar un usuario específico por su ID
});


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



Route::middleware('auth:sanctum')->prefix('admin/productos')->group(function () {
    Route::get('/', [ProductoController::class, 'index']); // Obtener la lista de todos los productos
    Route::post('/', [ProductoController::class, 'store']);  // Crear un nuevo producto
    Route::get('{id}', [ProductoController::class, 'show']);  // Obtener los detalles de un producto específico por su ID
    Route::put('{id}', [ProductoController::class, 'update']);  // Actualizar la información de un producto específico por su ID
    Route::delete('{id}', [ProductoController::class, 'destroy']); // Eliminar un producto específico por su ID
});


Route::middleware('auth:sanctum')->prefix('admin/detalles-productos')->group(function () {

    // Ruta para listar todos los detalles de productos
    Route::get('/', [DetalleProductoController::class, 'index']);
    // Obtener todos los detalles de productos. Ejemplo: Ver todos los productos con sus detalles (colores, tallas, precios, stock)
    Route::post('/', [DetalleProductoController::class, 'store']);

    // Ruta para crear un nuevo detalle de producto
    // Crear un nuevo detalle de producto. Se debe proporcionar la información del producto (ID), color (ID), talla (ID), precio, stock e imagen.

    // Ruta para obtener un detalle específico de producto
    Route::get('{id}', [DetalleProductoController::class, 'show']);
    // Ver un detalle de producto específico por su ID. Esto incluirá información como el producto, color, talla y stock de ese detalle en particular.

    // // Ruta para actualizar un detalle de producto existente
     Route::post('{id}', [DetalleProductoController::class, 'update']);
     Route::patch('{id}', [DetalleProductoController::class, 'updatePartial']);
    // Ruta para eliminar un detalle de producto específico
    Route::delete('{id}', [DetalleProductoController::class, 'destroy']);
    // Eliminar un detalle de producto por su ID. Esto puede eliminar variaciones de un producto que ya no estén disponibles.
    // Route::post('/images/{id}', 'updateImageById');

});




Route::get('/admin/detalle/all', [DetalleProductoController::class, 'all']);

// Ruta pública (no requiere autenticación)
Route::get('public/categorias', [CategoriaController::class, 'index']);
Route::get('public/variante-productos', [DetalleProductoController::class, 'index']);
Route::get('public/variante-productos/{id}', [DetalleProductoController::class, 'show']);
Route::get('public/categorias/{id}', [CategoriaController::class, 'show']); // Mostrar una categoría específica


Route::post('public/orden', [OrdenController::class, 'crearOrden']); // Obtener la lista de todos los productos
Route::get('public/ordenes', [OrdenController::class, 'listarOrdenes']);
Route::get('public/fechas', [OrdenController::class, 'listarFechas']);

Route::put('/public/ordenes/{id}', [OrdenController::class, 'actualizarOrden']);
Route::delete('/ordenes/{id}', [OrdenController::class, 'eliminarOrden']);
Route::get('/usuarios/{usuario_id}/ordenes', [OrdenController::class, 'listarOrdenesPorUsuario']);

Route::post('public/calcular-fecha-entrega', [OrdenController::class, 'calcularFechaEntrega']);
Route::get('/public/ordenes/usuario/{usuarioId}', [OrdenController::class, 'obtenerOrdenesPorUsuario']);

Route::post('/reportes/ingresos-mensuales', [ReportesController::class, 'ingresosMensuales']);

Route::get('/public/statistics', [DashbordController::class, 'getStatistics']);

// use App\Http\Controllers\PaymentController;

// Route::post('/create-order', [PaymentController::class, 'createOrder']);
// Route::post('/capture-order', [PaymentController::class, 'captureOrder']);

Route::apiResource('descuentos', DescuentoController::class);
Route::patch('descuentos/{descuento}/toggle-active', [DescuentoController::class, 'toggleActive']);
// Route::post('detalle-producto/{detalleProducto}/aplicar-descuento', [DescuentoController::class, 'aplicarDescuento']);
Route::post('aplicar-descuento', [DescuentoController::class, 'aplicarDescuento']);