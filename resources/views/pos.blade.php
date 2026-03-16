<x-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-screen w-full bg-gray-100 overflow-hidden">

        <div class="col-span-2 p-6 overflow-y-auto">
            <div class="flex justify-between mb-6">
                <h1 class="text-2xl font-bold">Menu Order</h1>
                {{-- <input type="text" placeholder="Search menu..." class="rounded-lg border-gray-300"> --}}
            </div>

            <div class="grid grid-cols-3 gap-4">
                @forelse ($products as $product)
                    <div
                        class="bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 hover:border-blue-500 hover:shadow-md transition-all group">
                        {{-- Image Container --}}
                        <div
                            class="aspect-square w-full mb-4 overflow-hidden rounded-[1.5rem] bg-slate-50 border border-slate-50">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                    <span class="text-slate-400 font-black text-xl">
                                        {{ strtoupper(substr($product->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Product Info --}}
                        <div class="space-y-1 mb-4 px-1">
                            <h3 class="font-black text-slate-800 leading-tight truncate">{{ $product->name }}</h3>
                            <p class="text-blue-600 font-black text-lg">₱{{ number_format($product->price, 2) }}</p>
                        </div>

                        {{-- Action --}}
                        <form action="{{ route('pos.addToCart', $product->id) }}" method="POST">
                            @csrf
                            <button
                                class="w-full bg-slate-900 text-white py-3 rounded-2xl text-sm font-bold active:scale-95 transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add to Cart
                            </button>
                        </form>
                    </div>
                @empty
                    {{-- Empty State - Spans the whole grid --}}
                    <div
                        class="col-span-full py-20 flex flex-col items-center justify-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-[3rem]">
                        <div class="opacity-30 mb-4">
                            {{-- Coffee/Shop icon --}}
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-400 text-center">No products found</h3>
                        <p class="text-slate-400 text-sm mt-2">Ask the admin to add items to the menu.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="col-span-1 bg-white border-l flex flex-col h-screen shadow-2xl">
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
                        Pay
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('receipt')
        <div class="receipt-container mx-auto text-base leading-tight font-mono text-black p-2" style="width: 120mm;">
            {{-- Header --}}
            <div class="text-center mb-6">
                <h2 class="font-bold text-2xl uppercase">AL POS SYSTEM</h2>
                <p class="text-sm">San Pedro, Laguna, Philippines</p>
                <p>VAT Reg TIN: 000-123-456-000</p>
                <p>{{ now()->format('M d, Y h:i A') }}</p>
            </div>

            {{-- Divider --}}
            <div class="border-b border-dashed border-black mb-2"></div>

            {{-- Items Table --}}
            <table class="w-full mb-2 text-sm">
                <thead>
                    <tr class="text-left border-b border-dashed border-black">
                        <th class="pb-1">ITEM</th>
                        <th class="text-right pb-1">QTY</th>
                        <th class="text-right pb-1">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @if (session('print_data'))
                        @foreach (collect(session('print_data')) as $item)
                            <tr>
                                <td class="pt-1 uppercase">{{ $item['name'] }}</td>
                                <td class="text-right pt-1">{{ $item['quantity'] }}</td>
                                <td class="text-right pt-1">₱{{ number_format($item['price'] * $item['quantity'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            {{-- Summary --}}
            <div class="border-t border-dashed border-black pt-2 space-y-1">
                <div class="flex justify-between">
                    <span>SUBTOTAL:</span>
                    @php $total = collect(session('print_data'))->sum(fn($i) => $i['price'] * $i['quantity']); @endphp
                    <span>₱{{ number_format($total, 2) }}</span>
                </div>
                <div class="flex justify-between text-xl font-black border-t border-black pt-1">
                    <span>TOTAL:</span>
                    <span>₱{{ number_format($total, 2) }}</span>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-6">
                <div class="border-b border-dashed border-black mb-2"></div>
                <p class="font-bold">ORDER #{{ date('md-His') }}</p>
                <p class="mt-2">This serves as your Official Receipt.</p>
                <p>Thank you for your purchase!</p>
                <p class="italic">Please come again.</p>
            </div>
        </div>
    @endpush

    @if (session('print_data'))
        <script>
            window.onload = function() {

                setTimeout(function() {
                    window.print();
                }, 4000);
            };
        </script>
    @endif
</x-layout>
