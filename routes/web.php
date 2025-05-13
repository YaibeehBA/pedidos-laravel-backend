<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/reset-password', function () {
    // Redirige al frontend de Vue con el token
    return redirect('http://localhost:5173/RestablecerContrasena?token=' . request('token'));
})-> name('reset-password');


