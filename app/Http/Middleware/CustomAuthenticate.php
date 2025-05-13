<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class CustomAuthenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            $guard = $this->guards[0] ?? null;
            if ($guard === 'admin') {
                return route('admin.login');
            } elseif ($guard === 'seller') {
                return route('seller.login');
            }
            // Eğer guard belirtilmemişse, fallback olarak seller.login yönlendirilebilir
            return route('seller.login');
        }
    }

}
