<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ReCaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    // app/Rules/ReCaptcha.php dosyasına hata ayıklama kodu ekleyin
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Gelen değeri ve anahtarı loglayalım
        \Log::info('ReCAPTCHA doğrulama girişimi', [
            'token' => $value,
            'secret' => config('services.recaptcha.secret_key')
        ]);

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        // Yanıtı loglayalım
        \Log::info('ReCAPTCHA API yanıtı', [
            'response' => $response->json()
        ]);

        if (!$response->json('success')) {
            $fail('reCAPTCHA doğrulaması başarısız oldu. Lütfen tekrar deneyin.');
        }
    }
}

