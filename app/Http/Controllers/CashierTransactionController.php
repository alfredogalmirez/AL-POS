<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CashierTransactionController extends Controller
{
    public function history(){
        $transactions = Order::with(['items.product', 'user'])->where('user_id', Auth::id())->latest()->paginate(10);

        return Inertia::render('POS/History', [
            'transactions' => $transactions,
        ]);
    }

    public function voidOrder(Order $order){
        if ($order->status === 'void'){
            return back()->with('message', 'This order is already voided.');
        }

        DB::beginTransaction();

            try {
                foreach($order->items as $item){
                    $item->product->increment('stock', $item->quantity);
                }

                $order->update(['status' => 'void']);

                DB::commit();
                return back()->with('success', 'Order voided successfully!');
            } catch (Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Failed to void: ' . $e->getMessage());
            }

    }
}
