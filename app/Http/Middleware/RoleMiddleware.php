<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Kullanıcı giriş yaptıysa ve rolü kontrol ediliyorsa
        $user = Auth::user();

        // Kullanıcı belirtilen role sahip değilse, erişim reddedilir
        if (!$user->hasRole($role)) {
            return abort(403, 'Unauthorized.');
        }

        // Kullanıcının rolü doğruysa işlemi devam ettir
        return $next($request);
    }
}
