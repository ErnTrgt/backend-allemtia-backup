<?php
namespace App\Http\Controllers\Api\home;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

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

    /** 5) ŞİFREMİ UNUTTUM */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Bu e-posta adresi kayıtlı değil.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Daha önceki token'ları temizle
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // 6 haneli rastgele bir kod oluştur
        $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Token'ı veritabanına kaydet
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($resetCode),
            'created_at' => Carbon::now()
        ]);

        // Mail gönder
        $mailSent = false;
        $mailError = '';

        try {
            // SSL pasif SMTP ayarlarını uygula
            Config::set('mail.host', 'mail.kurumsaleposta.com');
            Config::set('mail.port', 587);
            Config::set('mail.encryption', null); // SSL kapalı
            Config::set('mail.mailers.smtp.host', 'mail.kurumsaleposta.com');
            Config::set('mail.mailers.smtp.port', 587);
            Config::set('mail.mailers.smtp.encryption', null);

            // SSL doğrulama ayarları
            Config::set('mail.stream', [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                    'ciphers' => 'DEFAULT:!DH',
                ],
            ]);

            // İlk yöntem - standart Mail
            Mail::to($request->email)->send(new \App\Mail\ResetPasswordMail($resetCode));
            $mailSent = true;
        } catch (\Exception $e) {
            // Hata detayını kaydet
            $mailError = $e->getMessage();
            \Log::error('Şifre sıfırlama maili gönderirken hata (1. yöntem): ' . $e->getMessage());

            try {
                // İkinci yöntem - raw mail (daha basit)
                Mail::raw('Şifre sıfırlama kodunuz: ' . $resetCode, function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Allemtia - Şifre Sıfırlama Kodu');
                });
                $mailSent = true;
            } catch (\Exception $e2) {
                $mailError .= ' | İkinci deneme: ' . $e2->getMessage();
                \Log::error('Şifre sıfırlama maili gönderirken hata (2. yöntem): ' . $e2->getMessage());

                // Mail gönderimi başarısız
            }
        }

        $message = $mailSent
            ? 'Şifre sıfırlama kodu e-posta adresinize gönderildi.'
            : 'E-posta gönderimi başarısız oldu.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'mail_sent' => $mailSent,
            'mail_error' => $mailSent ? null : ($mailError ? substr($mailError, 0, 200) : null)
        ]);
    }

    /** 6) KODU DOĞRULA */
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Bypass modu - frontend'den gelen bypass_code parametresi varsa kontrolü atla
        $bypassMode = $request->has('bypass_mode') && $request->bypass_mode;

        if (!$bypassMode) {
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$resetRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz kod veya süresi dolmuş istek.'
                ], 422);
            }

            // Kodun 60 dakikadan eski olup olmadığını kontrol et
            $tokenCreatedAt = Carbon::parse($resetRecord->created_at);
            if (Carbon::now()->diffInMinutes($tokenCreatedAt) > 60) {
                // Eski kaydı sil
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama kodunun süresi doldu. Lütfen yeni kod talep edin.'
                ], 422);
            }

            // Kodu doğrula
            if (!Hash::check($request->code, $resetRecord->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz doğrulama kodu.'
                ], 422);
            }
        }

        // Başarılı doğrulama, şifre sıfırlama token'ı döndür
        $resetToken = Str::random(60);

        // Token'ı güncelle veya ekle
        DB::table('password_reset_tokens')
            ->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $resetToken,
                    'created_at' => Carbon::now()
                ]
            );

        return response()->json([
            'success' => true,
            'message' => 'Kod doğrulandı. Şimdi yeni şifrenizi belirleyebilirsiniz.',
            'reset_token' => $resetToken
        ]);
    }

    /** 7) ŞİFRE SIFIRLAMA */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || $resetRecord->token !== $request->token) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz token veya süresi dolmuş istek.'
            ], 422);
        }

        // Kullanıcının şifresini güncelle
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Kullanılan token'ı sil
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Şifreniz başarıyla güncellendi. Şimdi giriş yapabilirsiniz.'
        ]);
    }
}