<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetCode;

    /**
     * Create a new message instance.
     *
     * @param string $resetCode
     * @return void
     */
    public function __construct($resetCode)
    {
        $this->resetCode = $resetCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Basit düz metin içerik ekleyelim (fallback)
        $this->text('emails.reset-password-plain')
            ->subject('Allemtia - Şifre Sıfırlama Kodunuz');

        // Ayrıca markdown template'i de ekleyelim    
        return $this->subject('Allemtia - Şifre Sıfırlama Kodunuz')
            ->markdown('emails.reset-password', ['slot' => 'Allemtia'])
            ->with([
                'resetCode' => $this->resetCode,
            ]);
    }
}