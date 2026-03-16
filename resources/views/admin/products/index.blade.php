<x-admin-layout>
    <x-slot:title>Inventory Management</x-slot>

    <div class="p-8 bg-slate-50 min-h-screen">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-black text-slate-800">Inventory</h1>
                <a href="{{ route('admin.products.create') }}"
                    class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">
                    + Add Product
                </a>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-8 py-5 text-slate-500 font-bold uppercase text-xs tracking-wider">Product</th>
                            <th class="px-8 py-5 text-slate-500 font-bold uppercase text-xs tracking-wider">Price</th>
                            <th class="px-8 py-5 text-slate-500 font-bold uppercase text-xs tracking-wider">Stock</th>
                            <th class="px-8 py-5 text-slate-500 font-bold uppercase text-xs tracking-wider text-right">
                                Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-50">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-5 font-bold text-slate-800">
                                    {{ $product->name }}
                                </td>
                                <td class="px-8 py-5 text-slate-600">
                                    ₱{{ number_format($product->price, 2) }}
                                </td>
                                <td class="px-8 py-5">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-black {{ $product->stock < 5 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                        {{ $product->stock }} in stock
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right space-x-3">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="text-blue-600 hover:text-blue-800 font-bold text-sm">Edit</a>

                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                        class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 font-bold text-sm"
                                            onclick="return confirm('Delete this product?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        {{-- You can put a "box" or "inbox" icon here --}}
                                        <div class="text-slate-300 mb-4">
                                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-bold text-lg">Your inventory is empty</p>
                                        <p class="text-slate-400 text-sm mb-6">Start adding products to see them listed
                                            here.</p>
                                        <a href="{{ route('admin.products.create') }}"
                                            class="text-blue-600 font-black hover:underline">
                                            + Add your first product
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination Links --}}
                <div class="p-8 border-t border-slate-50">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
