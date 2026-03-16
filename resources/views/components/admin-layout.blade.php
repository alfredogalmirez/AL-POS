<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AL-POS Admin' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Google Fonts: Inter or Montserrat --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-72 bg-white border-r border-slate-100 flex flex-col sticky top-0 h-screen">
            <div class="p-8">
                <h1 class="text-2xl font-black tracking-tighter text-blue-600">AL-POS</h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Admin Panel</p>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <x-admin-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                    Dashboard
                </x-admin-nav-link>

                <x-admin-nav-link href="{{ route('admin.products.index') }}" :active="request()->routeIs('admin.products.index')">
                    Inventory
                </x-admin-nav-link>
            </nav>

            <div class="p-6 border-t border-slate-50">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="p-3 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 p-10">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div id="flash-message"
                    class="mb-6 p-4 bg-green-50 text-green-700 border border-green-100 rounded-2xl font-bold flex items-center shadow-sm">
                    <span class="mr-2">✓</span> {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('flash-message');
            if (alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';

                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 3000);
            }
        });
    </script>
</body>

</html>
