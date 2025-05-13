<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard === 'admin') {
                    return redirect('/admin/dashboard');
                }
                if ($guard === 'seller') {
                    return redirect('/seller/dashboard');
                }
                return redirect('/seller/login'); // Normal kullanıcılar için
            }
        }

        return $next($request);
    }
}
