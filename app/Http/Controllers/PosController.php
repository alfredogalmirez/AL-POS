<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\session;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::all();

        $cart = $request->session()->get('cart', []);

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('pos', compact('products', 'cart', 'total'));
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
        return redirect()->back();
    }

    public function checkout(Request $request)
    {
        $cart = collect($request->session()->get('cart'));

        if ($cart->isEmpty()) {
            return back()->with('error', 'Cart is empty!');
        }

        $total = $cart->sum(fn($item) => $item['price'] * $item['quantity']);

        DB::transaction(function () use ($cart, $total, $request) {
            $order = Order::create([
                'total' => $total,
                'payment_method' => $request->payment_method ?? 'cash',
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

        $request->session()->forget('cart');

        return redirect()->route('pos.index')->with('success', 'Transaction Completed!');
    }

    public function deleteFromCart(Product $product, Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if(isset($cart[$product->id])){
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
