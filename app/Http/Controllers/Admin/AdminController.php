<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(){
        $totalProducts = Product::count();
        $totalSales = 0;
        $lowStock = Product::where('stock', '<', 10)->count();

        return Inertia::render('Admin/Dashboard', [
            'totalProducts' => $totalProducts,
            'totalSales' => $totalSales,
            'lowStock' => $lowStock,
        ]);
    }
}
