<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CashierTransactionController extends Controller
{
    public function history(){
        $transactions = Order::with(['items.product', 'user'])->where('user_id', Auth::id())->latest()->paginate(10);

        return Inertia::render('POS/History', [
            'transactions' => $transactions,
        ]);
    }
}
