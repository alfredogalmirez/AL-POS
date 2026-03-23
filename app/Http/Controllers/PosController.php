<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

use function Pest\Laravel\session;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::all(['id', 'name']);

        $products = Product::query()->when($request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->get();

        return Inertia::render('POS/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category']),
            'total' => collect($request->session()->get('cart', []))->sum(fn($item) => $item['price'] * $item['quantity']),
            'cart' => $request->session()->get('cart', []),
        ]);
    }

    public function addToCart(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
            ];
        }

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Added to cart!');
    }

    public function checkout(Request $request)
    {
        $cart = collect($request->session()->get('cart'));

        if ($cart->isEmpty()) {
            return back()->with('error', 'Cart is empty!');
        }

        $total = $cart->sum(fn($item) => $item['price'] * $item['quantity']);
        $amountReceived = $request->amount_received;
        $change = $amountReceived - $total;

        $latestOrder = Order::latest('id')->first();
        $nextId = $latestOrder ? $latestOrder->id + 1 : 1;

        $invoiceId = 'ORD-' . now()->format('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($cart, $change, $invoiceId, $total, $request) {
            $order = Order::create([
                'invoice_id' => $invoiceId,
                'user_id' => Auth::id(),
                'total' => $total,
                'payment_method' => $request->payment_method ?? 'cash',
                'subtotal' => $total,
                'amount_received' => $request->amount_received,
                'change' => $change,
            ]);


            $items = $cart->map(fn($details, $id) => [
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $details['quantity'],
                'price' => $details['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->values()->all();

            OrderItem::insert($items);

            foreach ($cart as $id => $details) {
                Product::where('id', $id)->decrement('stock', $details['quantity']);
            }
        });

        $request->session()->flash('print_data', $cart);

        $summary = $request->session()->get('cart');
        $request->session()->forget('cart');

        return redirect()->route('pos.index')->with('success', 'Transaction Completed!')->with('print_data', $summary);
    }

    public function deleteFromCart(Product $product, Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            $request->session()->put('cart', $cart);
        }

        return back()->with('success', 'Item removed.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
