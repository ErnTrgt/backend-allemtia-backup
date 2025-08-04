<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    /**
     * Bakım modu yönetim sayfası
     */
    public function index()
    {
        $maintenance = MaintenanceSetting::latest()->first();
        return view('admin.maintenance.index', compact('maintenance'));
    }

    /**
     * Bakım modunu aç/kapa
     */
    public function toggle(Request $request)
    {
        try {
            $isActive = $request->input('is_active', false);
            
            if ($isActive) {
                // Yeni bakım modu oluştur veya güncelle
                $maintenance = MaintenanceSetting::firstOrNew(['is_active' => true]);
                $maintenance->fill([
                    'is_active' => true,
                    'title' => 'Site Bakımda',
                    'message' => 'Sitemiz şu anda bakım çalışması nedeniyle geçici olarak hizmet verememektedir. En kısa sürede geri döneceğiz.',
                    'created_by' => auth()->id()
                ]);
                $maintenance->save();
                
                Log::info('Bakım modu aktifleştirildi', ['user_id' => auth()->id()]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Bakım modu aktifleştirildi'
                ]);
            } else {
                // Tüm aktif bakım modlarını kapat
                MaintenanceSetting::where('is_active', true)->update(['is_active' => false]);
                
                Log::info('Bakım modu kapatıldı', ['user_id' => auth()->id()]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Bakım modu kapatıldı'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Bakım modu değiştirilirken hata', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu'
            ], 500);
        }
    }

    /**
     * Bakım modu ayarlarını güncelle
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'template_type' => 'required|in:simple,detailed,custom',
            'estimated_end_time' => 'nullable|date|after:now',
            'allowed_ips' => 'nullable|array',
            'allowed_ips.*' => 'ip',
            'notify_emails' => 'nullable|array',
            'notify_emails.*' => 'email'
        ]);

        try {
            $maintenance = MaintenanceSetting::where('is_active', true)->firstOrFail();
            
            $maintenance->update([
                'title' => $request->title,
                'message' => $request->message,
                'template_type' => $request->template_type,
                'estimated_end_time' => $request->estimated_end_time,
                'allowed_ips' => $request->allowed_ips ?? [],
                'notify_emails' => $request->notify_emails ?? []
            ]);

            Log::info('Bakım modu ayarları güncellendi', [
                'user_id' => auth()->id(),
                'maintenance_id' => $maintenance->id
            ]);

            return redirect()->route('admin.maintenance.index')
                ->with('success', 'Bakım modu ayarları güncellendi');
        } catch (\Exception $e) {
            Log::error('Bakım modu güncellenirken hata', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Ayarlar güncellenirken bir hata oluştu')
                ->withInput();
        }
    }

    /**
     * Bakım modu durumunu kontrol et (AJAX)
     */
    public function status()
    {
        $maintenance = MaintenanceSetting::getActive();
        
        return response()->json([
            'is_active' => $maintenance ? true : false,
            'maintenance' => $maintenance
        ]);
    }
}