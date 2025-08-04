<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\MaintenanceSetting;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Aktif bakım modunu kontrol et
        $maintenanceMode = MaintenanceSetting::getActive();

        if ($maintenanceMode) {
            // Admin ve API rotalarını bypass et
            if ($request->is('admin/*') || $request->is('api/*') || $request->is('seller/*')) {
                return $next($request);
            }

            // Login rotasını bypass et (admin girişi için)
            if ($request->is('login') || $request->is('*/login')) {
                return $next($request);
            }

            // İzinli IP kontrolü
            $clientIp = $request->ip();
            if ($maintenanceMode->isIpAllowed($clientIp)) {
                return $next($request);
            }

            // Giriş yapmış admin kontrolü
            if (auth()->check() && auth()->user()->isAdmin()) {
                return $next($request);
            }

            // API isteği ise JSON döndür
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $maintenanceMode->title,
                    'details' => $maintenanceMode->message,
                    'estimated_end_time' => $maintenanceMode->estimated_end_time
                ], 503);
            }

            // Bakım sayfasını göster
            return response()->view('errors.503', [
                'maintenance' => $maintenanceMode
            ], 503);
        }

        return $next($request);
    }
}