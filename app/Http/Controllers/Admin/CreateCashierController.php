<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class CreateCashierController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/Cashier/CashierCreate');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,cashier',
        ]);

        $finalUsername = 'CSR-' . $validated['username'];

        User::create([
            'name' => $validated['name'],
            'username' => $finalUsername,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'New Cashier Added!');
    }
}
