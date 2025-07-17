<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class DirectMailTest extends Command
{
    protected $signature = 'mail:direct {email?}';
    protected $description = 'PHPMailer kullanarak doğrudan SMTP ile mail gönder';

    public function handle()
    {
        $email = $this->argument('email') ?: 'muskirp42@gmail.com';

        // PHPMailer yüklenmişse kullan
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $this->error('PHPMailer yüklü değil. Lütfen önce yükleyin:');
            $this->info('composer require phpmailer/phpmailer');
            return Command::FAILURE;
        }

        $this->info('Doğrudan SMTP ile mail gönderimi test ediliyor...');

        try {
            // PHPMailer ile doğrudan SMTP ayarları
            $mail = new PHPMailer(true);

            // Debug bilgisi göster
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;

            // SMTP ayarları - SSL pasif
            $mail->isSMTP();
            $mail->Host = 'mail.kurumsaleposta.com';
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME', 'bilgi@erkpa.com.tr');
            $mail->Password = env('MAIL_PASSWORD', '');
            $mail->SMTPSecure = ''; // SSL kapalı
            $mail->Port = 587; // SSL pasif port

            // SSL ayarları
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ];

            // Kimden ve kime
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'bilgi@erkpa.com.tr'), env('MAIL_FROM_NAME', 'Allemtia'));
            $mail->addAddress($email);

            // İçerik
            $mail->isHTML(true);
            $mail->Subject = 'Doğrudan SMTP Test Maili ' . date('H:i:s');
            $mail->Body = 'Bu mail PHPMailer ile doğrudan SMTP üzerinden gönderilmiştir. <br><br>Tarih: ' . now();
            $mail->AltBody = 'Bu mail PHPMailer ile doğrudan SMTP üzerinden gönderilmiştir. Tarih: ' . now();

            // Gönder
            $output = new \Symfony\Component\Console\Output\BufferedOutput();
            $mail->Debugoutput = function ($str, $level) use ($output) {
                $output->writeln($str);
            };

            $mail->send();
            $this->info($output->fetch());
            $this->info('Mail başarıyla gönderildi!');
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('Mail gönderilemedi: ' . $mail->ErrorInfo);
            if (isset($output)) {
                $this->info('Debug bilgisi:');
                $this->info($output->fetch());
            }
            return Command::FAILURE;
        }
    }
}