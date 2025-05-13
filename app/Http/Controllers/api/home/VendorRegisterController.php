<?php

namespace App\Http\Controllers\Api\home;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class VendorRegisterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shopName' => 'required|string|max:255',
            'bankAccountNumber' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'busnessAddress' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            // 'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
        ]);

        $user = User::create([
            'name' => $validated['shopName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'address' => $validated['busnessAddress'],
            'bank_account' => $validated['bankAccountNumber'] ?? null,
            // 'gender' => $validated['gender'] ?? null,
            'role' => 'seller',
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor registered successfully.',
            'data' => $user
        ]);
    }
}
