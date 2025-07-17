<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TestMailConfig extends Command
{
    protected $signature = 'mail:test {email?}';
    protected $description = 'Mail ayarlarını test et ve belirtilen adrese bir test e-postası gönder';

    public function handle()
    {
        $email = $this->argument('email') ?: 'muskirp42@gmail.com'; // Varsayılan alıcı

        // SSL pasif ayarları - Müşteri hizmetlerinden gelen alternatif ayar
        Config::set('mail.host', 'mail.kurumsaleposta.com');
        Config::set('mail.port', 587);
        Config::set('mail.encryption', null); // SSL kapalı

        // SSL doğrulama ayarları - bağlantı sorunlarını çözmek için
        Config::set('mail.stream', [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'ciphers' => 'DEFAULT:!DH',
            ],
        ]);

        $this->info('Mail ayarları:');
        $this->info('Driver: ' . config('mail.mailer'));
        $this->info('Host: ' . config('mail.host'));
        $this->info('Port: ' . config('mail.port'));
        $this->info('Encryption: ' . config('mail.encryption'));
        $this->info('Username: ' . config('mail.username'));
        $this->info('From Address: ' . config('mail.from.address'));
        $this->info('From Name: ' . config('mail.from.name'));

        $this->info("\n{$email} adresine test maili gönderiliyor...");

        try {
            Mail::raw('Bu bir test e-postasıdır. Mail ayarlarınız çalışıyor! Tarih: ' . now(), function ($message) use ($email) {
                $message->to($email)
                    ->subject('Allemtia - Mail Ayarları Test ' . date('H:i:s'));
            });

            $this->info('Test e-postası başarıyla gönderildi!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('E-posta gönderilemedi: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}