<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // is_cancelled sütunu zaten var, sadece eksik olanları ekle

            // İptal nedeni için metin alanı
            if (!Schema::hasColumn('order_items', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable()->after('is_cancelled');
            }

            // İptal tarihi için timestamp
            if (!Schema::hasColumn('order_items', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Sadece eklediğimiz sütunları kaldır
            $table->dropColumn(['cancel_reason', 'cancelled_at']);
        });
    }
};
