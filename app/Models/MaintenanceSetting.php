<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'title',
        'message',
        'estimated_end_time',
        'allowed_ips',
        'template_type',
        'notify_emails',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allowed_ips' => 'array',
        'notify_emails' => 'array',
        'estimated_end_time' => 'datetime'
    ];

    /**
     * İlişkiler
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Aktif bakım modunu getir
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * IP adresinin izinli olup olmadığını kontrol et
     */
    public function isIpAllowed($ip)
    {
        if (empty($this->allowed_ips)) {
            return false;
        }

        return in_array($ip, $this->allowed_ips);
    }

    /**
     * Bakım modunu aktifleştir
     */
    public function activate()
    {
        // Diğer aktif bakım modlarını kapat
        self::where('is_active', true)->update(['is_active' => false]);
        
        $this->is_active = true;
        $this->save();
    }

    /**
     * Bakım modunu deaktif et
     */
    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * Tahmini bitiş zamanı geçmiş mi?
     */
    public function isExpired()
    {
        if (!$this->estimated_end_time) {
            return false;
        }

        return $this->estimated_end_time->isPast();
    }
}