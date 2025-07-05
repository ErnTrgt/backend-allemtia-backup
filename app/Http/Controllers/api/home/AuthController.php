<?php
namespace App\Http\Controllers\Api\home;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /** 1) BUYER REGISTER */
    public function register(Request $r)
    {
        $v = $r->validate([
            'fname' => 'required|string|max:50',
            'lname' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => 'required|min:6',
            'gender' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $v['fname'] . ' ' . $v['lname'],
            'email' => $v['email'],
            'phone' => $v['phone'],
            'password' => Hash::make($v['password']),
            'role' => 'buyer',
            'status' => 'approved',
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $user->id]], 201);
    }

    /** 2) BUYER LOGIN */
    public function login(Request $r)
    {
        $v = Validator::make($r->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'remember_me' => 'boolean' // Remember me ekledik
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $v->errors()
            ], 422);
        }

        $u = User::where('email', $r->email)->first();

        if (!$u || !Hash::check($r->password, $u->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kimlik bilgileri hatalı'
            ], 422);
        }

        if ($u->role !== 'buyer') {
            return response()->json([
                'success' => false,
                'message' => 'Bu bölüm sadece alıcılar içindir'
            ], 403);
        }

        // Eski token'ları sil
        $u->tokens()->delete();

        // Token süresini belirle
        $rememberMe = $r->boolean('remember_me', false);
        $expiresAt = $rememberMe ? now()->addDays(30) : now()->addDay();

        // Sanctum token oluştur
        $token = $u->createToken('auth-token', ['*'], $expiresAt)->plainTextToken;

        /* ---------- http-only cookie ---------- */
        $cookieMinutes = $rememberMe ? 60 * 24 * 30 : 60 * 24; // 30 gün veya 1 gün

        $cookie = cookie(
            'auth_token',
            $token,
            $cookieMinutes,
            '/',
            null,
            true,  // secure (https'de true olmalı)
            true,  // httpOnly
            false, // raw
            'Lax'  // SameSite (Lax daha uyumlu)
        );

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $u,
            'expires_at' => $expiresAt->toISOString(),
            'remember_me' => $rememberMe
        ])->withCookie(
                $cookie
            )->cookie('authToken', $token, 60 * 24, null, null, true, true);
    }

    /** 3) LOGOUT */
    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();

        // Cookie'yi temizle
        $cookie = cookie()->forget('auth_token');

        return response()->json(['success' => true])->withCookie($cookie);
    }

    /** 4) GET USER */
    public function user(Request $r)
    {
        return response()->json($r->user());
    }
}