<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        $totalProducts = Product::count();
        $totalSales = 0;
        $lowStock = Product::where('stock', '<', 10)->count();

        return view('admin.dashboard', compact('totalProducts', 'totalSales', 'lowStock'));
    }
}
