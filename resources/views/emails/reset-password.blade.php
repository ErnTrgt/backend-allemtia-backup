@component('mail::message')
# Şifre Sıfırlama Kodu

Şifre sıfırlama talebiniz alınmıştır.

Şifrenizi sıfırlamak için aşağıdaki kodu kullanın:

@component('mail::panel')
<div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px;">{{ $resetCode }}</div>
@endcomponent

Bu kod 1 saat boyunca geçerlidir.

Eğer bu talebi siz yapmadıysanız, herhangi bir işlem yapmanız gerekmez.

Teşekkürler,<br>
{{ config('app.name') }} Ekibi

<small>Bu e-posta otomatik olarak gönderilmiştir, lütfen yanıtlamayınız.</small>
@endcomponent