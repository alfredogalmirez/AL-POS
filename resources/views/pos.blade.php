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

        <div class="col-span-3 bg-white border-l p-4 flex flex-col">
            <h2 class="text-xl font-bold mb-4">Order Summary</h2>
            <div class="flex-1 overflow-y-auto">
                @if (session('cart'))
                    @foreach (session('cart') as $id => $details)
                        <div class="flex justify-between mb-4">
                            <span>{{ $details['name'] }} ({{ $details['quantity'] }})</span>
                            <span>₱{{ $details['price'] * $details['quantity'] }}</span>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between font-bold text-lg mb-4">
                    <span>Total</span>
                    <span>₱{{ $total }}</span>
                </div>
                <form action="" method="POST">
                    @csrf
                    <button class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold">Confirm Payment</button>
                </form>
            </div>
        </div>
    </div>
</x-layout>
