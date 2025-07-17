@component('mail::message')
# Allemtia - Şifre Sıfırlama

Sayın Müşterimiz,

Hesabınız için şifre sıfırlama talebinde bulundunuz. Şifrenizi sıfırlamak için aşağıdaki doğrulama kodunu kullanabilirsiniz:

@component('mail::panel')
<div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px;">{{ $resetCode }}</div>
@endcomponent

Bu doğrulama kodu **60 dakika** boyunca geçerlidir.

Eğer bu talebi siz yapmadıysanız, lütfen hesabınızın güvenliğini kontrol edin ve gerekirse bizimle iletişime geçin.

@component('mail::button', ['url' => config('app.url')])
Allemtia'ya Git
@endcomponent

Saygılarımızla,<br>
**Allemtia Müşteri Destek Ekibi**

<small>Bu e-posta otomatik olarak gönderilmiştir, lütfen yanıtlamayınız.</small>
@endcomponent 