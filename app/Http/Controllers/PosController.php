<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
            'total' => collect($request->session()->get('cart', []))->sum(fn($item) => $item['price'] * $item['quantity'] ?? 0),
            'cart' => $request->session()->get('cart', []),
        ]);
    }

    public function addToCart(Request $request, Product $product)
    {
        // Initialize Session for Cart
        $cart = $request->session()->get('cart', []);

        // Guard for Adding to Cart and for existing
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => 1,
                'price' => (float) $product->price,
            ];
        }

        //Save to the Cart Session
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Added to cart!');
    }

    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $id = $request->product_id;
        $change = $request->change;


        if (isset($cart[$id])) {
            $newQty = $cart[$id]['quantity'] + $change;

            if ($newQty < 1) {
                unset($cart[$id]);
            } else {
                $product = Product::find($id);

                if ($product && $newQty  > $product->stock) {
                    return back()->with('error', 'Not enough stock!');
                }

                $cart[$id]['quantity'] = $newQty;
            }

            $request->session()->put('cart', $cart);
        }

        return back();
    }

    public function setQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $request->session()->get('cart', []);
        $id = $request->product_id;
        $newQty = (int) $request->quantity;

        if (isset($cart[$id])) {
            if ($newQty <= 0) {
                unset($cart[$id]);
            } else {
                $product = Product::find($id);

                if ($product && $newQty > $product->stock) {
                    return back()->with('error', "Only {$product->stock} items in stock.");
                }

                $cart[$id]['quantity'] = $newQty;
            }

            $request->session()->put('cart', $cart);
        }

        return back();
    }

    public function checkout(Request $request)
    {
        $cart = collect($request->session()->get('cart'));

        if ($cart->isEmpty()) {
            return back()->with('error', 'Cart is empty!');
        }

        $amountReceived = $request->amount_received;

        $latestOrder = Order::latest('id')->first();
        $nextId = $latestOrder ? $latestOrder->id + 1 : 1;

        $invoiceId = 'ORD-' . now()->format('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        try {
            $this->proccessOrderInDatabase(
                $cart,
                $request->payment_method ?? 'cash',
                $request->amount_received,
                $invoiceId,
            );
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $request->session()->flash('print_data', $cart);

        $summary = $request->session()->get('cart');
        $request->session()->forget('cart');

        return redirect()->route('pos.index')->with('success', 'Transaction Completed!')->with('print_data', $summary);
    }

    public function initiatePaymongo(Request $request)
    {
        $cart = collect($request->session()->get('cart'));

        if ($cart->isEmpty()) {
            return response()->json(['error' => 'Cart is empty!'], 400);
        }

        $total = $cart->sum(fn($item) => $item['price'] * $item['quantity']);

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode(config('services.paymongo.secret') . ':'),
            'Content-Type' => 'application/json',
        ])->post('https://api.paymongo.com/v1/checkout_sessions', [
            'data' => [
                'attributes' => [
                    'send_email_receipt' => true,
                    'show_description' => true,
                    'payment_method_types' => ['gcash', 'paymaya'],
                    'currency' => 'PHP',
                    'description' => 'POS Order Items',
                    'line_items' => $cart->map(fn($item) => [
                        'amount' => (int)($item['price'] * 100), // in centavos
                        'currency' => 'PHP',
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                    ])->values()->all(),
                    'success_url' => route('pos.index', ['payment' => 'success']),
                    'cancel_url' => route('pos.index', ['payment' => 'failed'])
                ]
            ]
        ]);

        dd($response->json());

        if ($response->failed()) {
            return response()->json(['error' => 'Paymongo API Error'], 500);
        }

        return response()->json([
            'checkout_url' => $response->json()['data']['attributes']['checkout_url']
        ]);
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

    private function proccessOrderInDatabase($cart, $paymentMethod, $amountReceived, $invoiceId)
    {
        return DB::transaction(function () use ($cart, $paymentMethod, $amountReceived, $invoiceId) {
            $total = $cart->sum(fn($item) => $item['price'] * $item['quantity']);
            $change = $amountReceived - $total;

            $order = Order::create([
                'invoice_id' => $invoiceId,
                'user_id' => Auth::id(),
                'total' => $total,
                'payment_method' => $paymentMethod,
                'subtotal' => $total,
                'amount_received' => $amountReceived,
                'change' => $change,
            ]);

            $items = $cart->map(fn($details, $id) => [
                'order_id'   => $order->id,
                'product_id' => $id,
                'quantity'   => $details['quantity'],
                'price'      => $details['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->values()->all();

            OrderItem::insert($items);

            foreach ($cart as $id => $details) {
                Product::where('id', $id)->decrement('stock', $details['quantity']);
            }

            return $order;
        });
    }
}
