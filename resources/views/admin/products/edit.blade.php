<x-admin-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('admin.products.index') }}" class="text-slate-400 hover:text-slate-800 font-bold transition-colors">
                ← Back to Inventory
            </a>
            <h1 class="text-3xl font-black text-slate-800 mt-4">Edit Product</h1>
        </div>

        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    {{-- Left Side: Image Preview & Upload --}}
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-slate-700 uppercase tracking-widest">Product Image</label>
                        <div class="aspect-square rounded-[2rem] bg-slate-50 overflow-hidden border border-slate-100">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default.png') }}"
                                 class="w-full h-full object-cover" id="preview">
                        </div>
                        <input type="file" name="image" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-600 font-bold">
                    </div>

                    {{-- Right Side: Fields --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Product Name</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                                   class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 transition-all outline-none" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Price (₱)</label>
                                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}"
                                       class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 transition-all outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Stock</label>
                                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                                       class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 transition-all outline-none" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                            <textarea name="description" rows="4"
                                      class="w-full px-5 py-4 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 transition-all outline-none">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50">
                    <button type="submit" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black text-xl hover:bg-black transition-all shadow-xl shadow-slate-200">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
