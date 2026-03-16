<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function history(){
        $transactions = Order::with(['items.product', 'user'])->where('user_id', Auth::id())->latest()->paginate(15);

        return view('transaction', compact('transactions'));
    }
}
