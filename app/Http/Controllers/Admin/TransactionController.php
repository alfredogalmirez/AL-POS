<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index() {

        $stats = [
            'today_total' => Order::whereDate('created_at', today())->sum('total'),
            'count' => Order::count(),
            'avg' => Order::avg('total') ?? 0,
        ];


        $orders = Order::with(['items.product', 'cashier'])->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

            return Inertia::render('Admin/Transactions/Index', [
                'transactions' => $orders,
                'stats' => $stats,
            ]);
    }

    public function export(){

        $fileName = 'POS_Export_' . date('Y-m-d_His') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Date', 'Invoice', 'Cashier', 'Product', 'Qty', 'Price', 'Total', 'Payment'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $columns);

            Order::with(['items.product', 'cashier'])->chunk(100, function ($orders) use ($file) {
                foreach($orders as $order){
                    foreach($order->items as $item) {
                      fputcsv($file, [
                        $order->created_at->format('Y-m-d H:i'),
                        $order->id ?? 'N/A',
                        $order->cashier->name ?? 'System',
                        $item->product->name ?? 'N/A',
                        $item->quantity,
                        $item->price,
                        $item->quantity * $item->price,
                        strtoupper($order->payment_method ?? 'CASH'),
                    ]);
                    }
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
