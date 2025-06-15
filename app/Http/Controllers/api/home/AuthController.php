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
            'role' => 'buyer',      // ◄◄  kritik
            'status' => 'approved',
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $user->id]], 201);
    }

    /** 2) BUYER LOGIN */
    // GİRİŞ
    public function login(Request $r)
    {
        $v = Validator::make($r->all(), [
            'email' => 'required|email',
            'password' => 'required',
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

        // Sanctum token oluştur
        $token = $u->createToken('api')->plainTextToken;

        /* ---------- http-only cookie ---------- */
        $cookie = cookie(
            'auth_token',          // ad
            $token,                // değer
            60 * 24,               // dakika (1 gün)
            '/',                   // yol
            null,                  // domain (varsayılan)
            true,                  // secure  (https’de true olmalı)
            true,                  // httpOnly
            false,                 // raw
            'Strict'               // SameSite
        );

        // Kullanıcı verisini döndür, token yok!
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $u  // {id,fname,lname,role...}
        ])->withCookie($cookie);
    }

    /** 3) LOGOUT */
    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();
        return response()->json(['success' => true]);
    }
}
