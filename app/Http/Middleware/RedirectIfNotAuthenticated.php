<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (!Auth::guard($guard)->check()) {
            if ($guard === 'admin') {
                return redirect()->route('admin.login');
            } elseif ($guard === 'seller') {
                return redirect()->route('seller.login');
            }
        }
        return $next($request);
    }
}
