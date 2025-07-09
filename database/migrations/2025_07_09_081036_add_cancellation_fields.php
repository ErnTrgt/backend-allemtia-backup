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
        // Orders tablosuna iptal ve takip için eksik alanları ekle
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('notes');
            $table->text('status_note')->nullable()->after('tracking_number');
            $table->text('cancellation_reason')->nullable()->after('status_note');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->text('seller_note')->nullable()->after('cancelled_at');
            $table->boolean('is_partially_cancelled')->default(false)->after('seller_note');
        });

        // Order items tablosuna iptal için gerekli alanları ekle
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_cancelled')->default(false)->after('subtotal');
            $table->text('cancellation_reason')->nullable()->after('is_cancelled');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_number',
                'status_note',
                'cancellation_reason',
                'cancelled_at',
                'seller_note',
                'is_partially_cancelled'
            ]);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'is_cancelled',
                'cancellation_reason',
                'cancelled_at'
            ]);
        });
    }
};