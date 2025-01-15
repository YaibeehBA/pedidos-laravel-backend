<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Orden;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashbordController extends Controller
{
    // En DashboardController.php

public function getStatistics()
{
    $totalOrders = Orden::count();
    $totalProducts = Producto::count();
    $totalCategories = Categoria::count();
    $totalUsers = User::count();

    return response()->json([
        'totalOrders' => $totalOrders,
        'totalProducts' => $totalProducts,
        'totalCategories' => $totalCategories,
        'totalUsers' => $totalUsers
    ]);
}

}
