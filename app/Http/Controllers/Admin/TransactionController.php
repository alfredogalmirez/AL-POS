<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date');
        $month = $request->input('month');
        $year = $request->input('year');

        $statsQuery = Order::query()
            ->when($date, fn($q) => $q->whereDate('created_at', $date))
            ->when($month, fn($q) => $q->whereMonth('created_at', $month))
            ->when($year, fn($q) => $q->whereYear('created_at', $year));

        $isFiltering = $date || $month || $year;

        $stats = [
            'today_total' => $isFiltering ? (clone $statsQuery)->sum('total') : Order::whereDate('created_at', today())->sum('total'),
            'count' => (clone $statsQuery)->count(),
            'avg' => (clone $statsQuery)->avg('total') ?? 0,
        ];


        $orders = Order::with(['items.product', 'cashier'])
            ->withCount('items')
            ->when($date, fn($q) => $q->whereDate('created_at', $date))
            ->when($month, fn($q) => $q->whereMonth('created_at', $month))
            ->when($year, fn($q) => $q->whereYear('created_at', $year))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Transactions/Index', [
            'transactions' => $orders,
            'stats' => $stats,
            'filters' => $request->only(['date', 'month', 'year'])
        ]);
    }

    public function export()
    {

        $fileName = 'POS_Export_' . date('Y-m-d_His') . '.csv';

        while (ob_get_level() > 0) ob_end_clean();

        $fileName = 'POS_Export_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new TransactionsExport, $fileName);
    }
}
