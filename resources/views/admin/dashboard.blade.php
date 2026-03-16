<x-admin-layout>
    <div class="p-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-black text-slate-800 mb-8">Admin Dashboard</h1>

            {{-- Bento Grid Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <p class="text-slate-500 font-bold uppercase text-xs tracking-wider">Total Products</p>
                    <h2 class="text-4xl font-black text-slate-900 mt-2">{{ $totalProducts }}</h2>
                </div>

                <div class="bg-blue-600 p-6 rounded-[2rem] shadow-lg shadow-blue-100 text-white">
                    <p class="text-blue-100 font-bold uppercase text-xs tracking-wider">Low Stock Alerts</p>
                    <h2 class="text-4xl font-black mt-2">{{ $lowStock }}</h2>
                </div>

                <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                    <p class="text-slate-500 font-bold uppercase text-xs tracking-wider">Daily Sales</p>
                    <h2 class="text-4xl font-black text-slate-900 mt-2">₱{{ number_format($totalSales, 2) }}</h2>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Inventory Management</h3>
                    <a href="{{ route('admin.products.index') }}" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-black transition-all">
                        Manage Products
                    </a>
                </div>
                <p class="text-slate-500">Update pricing, adjust stock levels, and add new items to your POS menu.</p>
            </div>
        </div>
    </div>
</x-admin-layout>
