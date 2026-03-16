<x-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-50">
        <div class="w-full max-w-md bg-white p-10 rounded-[2rem] shadow-xl border border-slate-100">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-slate-800 tracking-tight">AL POS</h1>
                <p class="text-slate-500 mt-2 font-medium">Log in to your account</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}"
                           class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 transition-all outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-3 rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 transition-all outline-none" required>
                </div>

                @if($errors->any())
                    <p class="text-red-500 text-sm font-medium">{{ $errors->first() }}</p>
                @endif

                <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-slate-200 transition-all active:scale-[0.98]">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</x-layout>
