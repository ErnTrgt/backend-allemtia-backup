<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GoogleAuthController extends Controller
{
    /**
     * Google'a yönlendirme
     */
    public function redirectToGoogle()
    {
        try {
            Log::info('Google OAuth yönlendirmesi başlatılıyor');
            return Socialite::driver('google')->stateless()->redirect();
        } catch (\Exception $e) {
            Log::error('Google yönlendirme hatası: ' . $e->getMessage());
            return redirect('/login?error=google_redirect_failed');
        }
    }

    /**
     * Google'dan gelen geri dönüş
     */
    public function handleGoogleCallback()
    {
        try {
            Log::info('Google callback işlemi başlatılıyor');
            $googleUser = Socialite::driver('google')->stateless()->user();

            Log::info('Google kullanıcısı alındı', [
                'email' => $googleUser->email,
                'id' => $googleUser->id,
                'name' => $googleUser->name
            ]);

            // Status için izin verilen değerleri kontrol et
            $allowedStatusValues = $this->getStatusAllowedValues();
            $statusValue = in_array('approved', $allowedStatusValues) ? 'approved' :
                (in_array('active', $allowedStatusValues) ? 'active' : '1');

            Log::info('Status için kullanılacak değer: ' . $statusValue);

            // Kullanıcı e-posta ile mevcut mu kontrol et
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                Log::info('Mevcut kullanıcı bulundu, güncelleniyor', ['user_id' => $existingUser->id]);
                // Kullanıcı zaten varsa Google ID güncelle
                if (empty($existingUser->google_id)) {
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar
                    ]);
                    Log::info('Mevcut kullanıcının google_id alanı güncellendi');
                }

                $user = $existingUser;
            } else {
                Log::info('Yeni kullanıcı oluşturuluyor');
                // Kullanıcı yoksa yeni bir kullanıcı oluştur
                $user = new User();
                $user->name = $googleUser->name;
                $user->email = $googleUser->email;
                $user->google_id = $googleUser->id;
                $user->password = Hash::make(Str::random(24)); // Rastgele güçlü şifre
                $user->avatar = $googleUser->avatar;
                $user->role = 'buyer'; // Varsayılan olarak alıcı rolü
                $user->status = $statusValue; // Veritabanı sütununa göre uygun değer

                // Veritabanına kaydet
                $saved = $user->save();

                if (!$saved) {
                    Log::error('Kullanıcı kaydedilemedi');
                    throw new \Exception('Kullanıcı kaydedilemedi');
                }

                Log::info('Yeni kullanıcı oluşturuldu', ['user_id' => $user->id]);

                // Buyer rolünü ata (eğer Spatie Permission kullanıyorsanız)
                try {
                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('buyer');
                        Log::info('Buyer rolü atandı');
                    }
                } catch (\Exception $e) {
                    Log::warning('Rol atama hatası: ' . $e->getMessage());
                }
            }

            // Uzun ömürlü token oluştur (30 gün)
            $expiration = now()->addDays(30);
            $token = $user->createToken('auth_token', ['*'], $expiration)->plainTextToken;
            Log::info('Kullanıcı için token oluşturuldu (30 günlük)');

            // Frontend URL'yi env'den al, yoksa varsayılan değer kullan
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');

            // Token ve user bilgilerini frontend'e geçir
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar
            ];

            $redirectUrl = "{$frontendUrl}/login/callback?" . http_build_query([
                'token' => $token,
                'user' => json_encode($userData),
                'expires_at' => $expiration->timestamp * 1000,
                'success' => true
            ]);

            Log::info('Frontend\'e yönlendiriliyor', ['url' => $redirectUrl]);

            // Frontend'e yönlendir
            return redirect($redirectUrl);

        } catch (\Exception $e) {
            Log::error('Google login hatası: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            return redirect("{$frontendUrl}/login?error=google_auth_failed&message=" . urlencode($e->getMessage()));
        }
    }

    /**
     * Status sütunu için izin verilen değerleri al
     */
    private function getStatusAllowedValues()
    {
        try {
            // MySQL'de status sütununun tipini ve kısıtlamalarını alıyoruz
            $tableName = (new User)->getTable(); // "users"

            // MySQL için ENUM tipini kontrol et
            $columnInfo = DB::select("SHOW COLUMNS FROM $tableName WHERE Field = 'status'");

            if (!empty($columnInfo) && isset($columnInfo[0]->Type)) {
                $type = $columnInfo[0]->Type;

                // ENUM('value1','value2') formatından değerleri çıkar
                if (strpos($type, 'enum') === 0) {
                    preg_match("/^enum\('(.*)'\)$/", $type, $matches);
                    if (isset($matches[1])) {
                        // 'value1','value2' -> [value1, value2]
                        $values = explode("','", $matches[1]);
                        return $values;
                    }
                }
            }

            // Varsayılan olarak bazı genel değerleri döndür
            return ['approved', 'active', 'pending', '1', '0'];

        } catch (\Exception $e) {
            Log::warning('Status değerleri alınamadı: ' . $e->getMessage());
            // Varsayılan değerleri döndür
            return ['approved', 'active', 'pending', '1', '0'];
        }
    }
}



