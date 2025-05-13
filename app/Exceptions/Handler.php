<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Route;

class Handler extends ExceptionHandler
{
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // Kullanıcının hangi guard ile giriş yaptığını belirle
        $guards = $exception->guards();
        $guard = $guards[0] ?? 'seller'; // Varsayılan olarak seller olarak kabul et

        // Eğer giriş yapan kullanıcı admin ise ve admin login route'u varsa yönlendir
        if ($guard === 'admin' && Route::has('admin.login')) {
            return redirect()->guest(route('admin.login'));
        }
        // Eğer giriş yapan kullanıcı seller ise ve seller login route'u varsa yönlendir
        elseif ($guard === 'seller' && Route::has('seller.login')) {
            return redirect()->guest(route('seller.login'));
        }

        // Eğer guard belirlenemezse, varsayılan olarak seller login sayfasına yönlendir
        return $request->expectsJson()
            ? response()->json(['message' => 'Unauthenticated.'], 401)
            : redirect()->guest(route('seller.login'));
    }
}
