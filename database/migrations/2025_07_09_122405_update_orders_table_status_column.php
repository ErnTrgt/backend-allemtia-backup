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
        // Sadece is_partially_cancelled sütununu ekle (eğer yoksa)
        if (!Schema::hasColumn('orders', 'is_partially_cancelled')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('is_partially_cancelled')->default(false)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri dönüşte sütunu kaldır
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_partially_cancelled');
        });
    }
};
