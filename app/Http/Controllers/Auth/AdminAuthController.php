<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // Login formunu göster
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // Giriş işlemi
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // // Kullanıcı giriş yaparsa
        // if (Auth::attempt($request->only('email', 'password'))) {
        //     $user = auth()->user();

        //     // Kullanıcının 'admin' rolünü kontrol et
        //     if ($user->hasRole('admin')) {
        //         return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
        //     } else {
        //         Auth::logout();
        //         return back()->withErrors(['email' => 'Unauthorized role.']);
        //     }
        // }

        // // Giriş başarısızsa
        // return back()->withErrors(['email' => 'Invalid credentials.']);
        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            $user = Auth::guard('admin')->user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
            }

            Auth::guard('admin')->logout();
            return back()->withErrors(['email' => 'You are not authorized as an admin.']);
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    // Çıkış işlemi
    // public function logout(Request $request)
    // {
    //     Auth::logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    // }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
