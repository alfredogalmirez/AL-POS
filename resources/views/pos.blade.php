<x-layout>
    <div class="grid grid-cols-12 h-screen w-full bg-gray-100">
        <div class="col-span-1 bg-white border-r flex flex-col items-center py-4">
        </div>

        <div class="col-span-8 p-6 overflow-y-auto">
            <div class="flex justify-between mb-6">
                <h1 class="text-2xl font-bold">Menu Order</h1>
                <input type="text" placeholder="Search menu..." class="rounded-lg border-gray-300">
            </div>

            <div class="grid grid-cols-3 gap-4">
                @foreach ($products as $product)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-transparent hover:border-blue-500">
                        <div class="h-32 bg-gray-200 rounded-lg mb-2"></div>
                        <h3 class="font-semibold">{{ $product->name }}</h3>
                        <p class="text-blue-600 font-bold">₱{{ number_format($product->price, 2) }}</p>
                        <form action="{{ route('pos.addToCart', $product->id) }}" method="POST">
                            @csrf
                            <button class="w-full mt-2 bg-slate-800 text-white py-2 rounded-lg text-sm">+ Add to
                                Cart</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-span-3 bg-white border-l flex flex-col h-screen shadow-2xl">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-black text-slate-800">Current Order</h2>
                <p class="text-sm text-slate-400">Order #{{ date('md-His') }}</p>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 no-scrollbar">
                @if (session('cart') && count(session('cart')) > 0)
                    @foreach (session('cart') as $id => $details)
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center font-bold text-slate-700">
                                    {{ $details['quantity'] }}x
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 leading-tight">{{ $details['name'] }}</p>
                                    <p class="text-xs text-slate-400">₱{{ number_format($details['price'], 2) }} per
                                        unit</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-700">
                                    ₱{{ number_format($details['price'] * $details['quantity'], 2) }}
                                </span>

                                <form action="{{ route('pos.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center justify-center h-full text-center space-y-4 opacity-40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 11V7a4 4 0 118 0m-9 5a4 4 0 00-8 0v4a2 2 0 002 2h10a2 2 0 002-2v-4a2 2 0 00-2-2z" />
                        </svg>
                        <p class="text-lg font-medium">Cart is empty</p>
                    </div>
                @endif
            </div>

            <div class="p-6 bg-slate-50 border-t space-y-4">
                <div class="space-y-2">
                    <div class="flex justify-between text-slate-500 text-sm">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500 text-sm">
                        <span>Tax (0%)</span>
                        <span>₱0.00</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-xl font-black text-slate-800">Total</span>
                        <span class="text-2xl font-black text-blue-600">₱{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <form action="{{ route('pos.checkout') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="peer hidden" checked>
                            <div
                                class="text-center py-2 rounded-lg border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 text-xs font-bold transition-all">
                                CASH
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="gcash" class="peer hidden">
                            <div
                                class="text-center py-2 rounded-lg border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 text-xs font-bold transition-all">
                                GCASH
                            </div>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-blue-200 transition-all active:scale-95">
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layout>
