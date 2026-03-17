<x-admin-layout>
    <div class="p-8 bg-slate-50 min-h-screen">
        <div class="max-w-2xl mx-auto">
            <div class="mb-8">
                <a href="{{ route('admin.products.index') }}"
                    class="text-slate-500 hover:text-slate-800 font-bold text-sm">
                    ← Back to Inventory
                </a>
                <h1 class="text-3xl font-black text-slate-800 mt-4">Add New Product</h1>
            </div>

            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Product Image</label>
                        <div class="relative group">
                            <input type="file" name="image" accept="image/*"
                                class="w-full px-4 py-8 border-2 border-dashed border-slate-200 rounded-[2rem] text-slate-400
                       file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                       file:text-sm file:font-black file:bg-blue-50 file:text-blue-600
                       hover:border-blue-300 transition-all cursor-pointer">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Product Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-blue-500 transition-all outline-none"
                            placeholder="e.g. Iced Latte" required>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Price (₱)</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                                class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-blue-500 transition-all outline-none"
                                placeholder="0.00" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Initial Stock</label>
                            <input type="number" name="stock" value="{{ old('stock', 0) }}"
                                class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-blue-500 transition-all outline-none"
                                required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Description (Optional)</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-blue-500 transition-all outline-none"
                            placeholder="Brief details about the item...">{{ old('description') }}</textarea>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 bg-red-50 text-red-600 rounded-2xl text-sm font-medium">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit"
                        class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-lg hover:bg-black transition-all shadow-lg shadow-slate-200">
                        Create Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
