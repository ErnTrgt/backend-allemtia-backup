<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerAuthController extends Controller
{
    // Login formunu göster
    public function showLoginForm()
    {
        return view('seller.auth.login');
    }

    // Giriş işlemi
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // 1. Kullanıcı var mı?
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Böyle bir e-posta ile kayıtlı kullanıcı bulunamadı.'
            ])->withInput();
        }

        // 2. Rolü seller mı?
        if ($user->role !== 'seller') {
            return back()->withErrors([
                'email' => 'Bu giriş sadece satıcılar içindir. Lütfen doğru paneli kullanın.'
            ])->withInput();
        }

        // 3. Hesap onaylı mı?
        if ($user->status !== 'approved') {
            $statusMessage = match ($user->status) {
                'pending' => 'Hesabınız henüz onay bekliyor. Lütfen yöneticiden onay alınız.',
                'rejected' => 'Hesabınız onaylanmadı. Lütfen bizimle iletişime geçin.',
                default => 'Hesabınız aktif değil.',
            };

            return back()->withErrors([
                'email' => $statusMessage
            ])->withInput();
        }

        // 4. Şifre doğru mu?
        if (!Auth::guard('seller')->attempt($credentials)) {
            return back()->withErrors([
                'email' => 'E-posta veya şifre hatalı.'
            ])->withInput();
        }

        // Başarılı giriş
        return redirect()->route('seller.dashboard')->with('success', 'Giriş başarılı!');
    }


    public function logout(Request $request)
    {
        Auth::guard('seller')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seller.login')->with('success', 'Logged out successfully.');
    }


}
