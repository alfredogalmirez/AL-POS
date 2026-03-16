<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }

            body * {
                visibility: hidden;
            }

            /* Hide everything on the screen */
            body>div:not(#receipt-print) {
                display: none !important;
            }

            body {
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                min-height: 100vh !important;
                margin: 0 !important;
                padding: 0 !important;
                background-color: white !important;
            }

            /* Only show the receipt and its children */
            #receipt-print {
                display: block !important;
                visibility: visible !important;
                width: 120mm;
                margin: 0 auto !important;
                padding: 40px;
                background: white;
                font-size: 16px;
                line-height: 1.5;
            }

            #receipt-print * {
                visibility: visible !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100 antialiased">
    <div class="flex min-h-screen">
        @auth
            <nav class="w-64 bg-white border-r">
                <div class="col-span-1 bg-white border-r flex flex-col items-center py-4">
                    <div class="flex flex-col h-screen gap-8">
                        <div
                            class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>

                        <div class="flex flex-col gap-4 flex-1">
                            <a href="{{ route('pos.index') }}"
                                class="p-3 text-blue-600 bg-blue-50 rounded-xl transition-all"><svg class="w-6 h-6"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                    </path>
                                </svg></a>
                            <a href="{{ route('pos.history') }}"
                                class="p-3 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all"><svg
                                    class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg></a>
                        </div>

                        <div class="pb-10">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf

                                <button
                                    class="p-3 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        @endauth

        {{-- Page Content --}}
        <main class="flex-1">
            <div class="fixed top-4 right-4 z-50 w-96 pointer-events-none">
                {{-- Success Message --}}
                @if (session('success'))
                    <div id="flash-message"
                        class="mb-6 p-4 bg-green-50 text-green-700 border border-green-100 rounded-2xl font-bold flex items-center shadow-sm">
                        <div class="flex items-center">
                            <span class="font-bold">Success!</span>
                            <span class="ml-2">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Error Message --}}
                @if (session('error'))
                    <div id="flash-message"
                        class="mb-6 p-4 bg-red-50 text-red-700 border border-red-400 rounded-2xl font-bold flex items-center shadow-sm">
                        <div class="flex items-center">
                            <span class="font-bold">Error!</span>
                            <span class="ml-2">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{ $slot }}
        </main>
    </div>

    <div id="receipt-print" class="hidden print:block">
        @stack('receipt')
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
