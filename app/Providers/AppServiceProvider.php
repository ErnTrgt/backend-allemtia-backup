<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // SSL pasif ayarları - Müşteri hizmetlerinden gelen alternatif ayar
        // Laravel 10+ için mail ayarları
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'mail.kurumsaleposta.com');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.encryption', null); // SSL kapalı // SSL kapalı

        // Eski yapı için (geriye dönük uyumluluk)
        Config::set('mail.host', 'mail.kurumsaleposta.com');
        Config::set('mail.port', 587);
        Config::set('mail.encryption', null); // SSL kapalı // SSL kapalı

        // SSL doğrulama ayarları
        Config::set('mail.mailers.smtp.verify_peer', false);
        Config::set('mail.mailers.smtp.verify_peer_name', false);
        Config::set('mail.mailers.smtp.allow_self_signed', true);

        // Eski yapı için stream ayarları
        Config::set('mail.stream', [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'ciphers' => 'DEFAULT:!DH',
            ],
        ]);
    }
}
