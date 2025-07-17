<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class FixMailConfig extends Command
{
    protected $signature = 'mail:fix';
    protected $description = 'Mail yapılandırmasını sıfırlar ve doğru ayarları uygular';

    public function handle()
    {
        $this->info('Mail yapılandırması düzeltiliyor...');

        // Tüm önbellekleri temizle
        $this->info('Önbellekler temizleniyor...');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('optimize:clear');

        // Laravel'in mail.php dosyasını güncellemeden direkt olarak yapılandırmayı değiştir
        $this->info('Mail ayarları uygulanıyor...');

        // Mail yapılandırmasını değiştir - SSL pasif ayarları
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailer', 'smtp');
        Config::set('mail.mailers.smtp.host', 'mail.kurumsaleposta.com');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.encryption', null); // SSL kapalı
        Config::set('mail.mailers.smtp.username', env('MAIL_USERNAME', 'bilgi@erkpa.com.tr'));
        Config::set('mail.mailers.smtp.password', env('MAIL_PASSWORD', ''));

        Config::set('mail.mailers.smtp.verify_peer', false);
        Config::set('mail.mailers.smtp.verify_peer_name', false);
        Config::set('mail.mailers.smtp.allow_self_signed', true);

        // AppServiceProvider'da yapılandırma değişikliği yapalım
        $this->updateAppServiceProvider();

        $this->info('Mail yapılandırması düzeltildi.');
        $this->info('Şimdi "php artisan mail:test" komutunu çalıştırabilirsiniz.');

        return Command::SUCCESS;
    }

    protected function updateAppServiceProvider()
    {
        $path = app_path('Providers/AppServiceProvider.php');

        if (file_exists($path)) {
            $content = file_get_contents($path);

            // Port ve encryption değerlerini güncelle - SSL pasif için
            $content = preg_replace(
                "/Config::set\('mail\.port',\s*[^)]*\);/",
                "Config::set('mail.port', 587);",
                $content
            );

            $content = preg_replace(
                "/Config::set\('mail\.encryption',\s*[^)]*\);/",
                "Config::set('mail.encryption', null); // SSL kapalı",
                $content
            );

            $content = preg_replace(
                "/Config::set\('mail\.mailers\.smtp\.port',\s*[^)]*\);/",
                "Config::set('mail.mailers.smtp.port', 587);",
                $content
            );

            $content = preg_replace(
                "/Config::set\('mail\.mailers\.smtp\.encryption',\s*[^)]*\);/",
                "Config::set('mail.mailers.smtp.encryption', null); // SSL kapalı",
                $content
            );

            file_put_contents($path, $content);
            $this->info('AppServiceProvider güncellendi.');
        }
    }
}