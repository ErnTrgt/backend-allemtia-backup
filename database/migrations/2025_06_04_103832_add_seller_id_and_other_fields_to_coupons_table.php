<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            // seller_id ekleyelim (nullable, users tablosuna foreignKey)
            $table->foreignId('seller_id')
                ->nullable()
                ->after('active')                // aktif sütunundan sonra gelebilir
                ->constrained('users')
                ->onDelete('set null');
            // $table->boolean('active')->default(true);

            // Eğer min_order_amount alanı eksikse ekleyelim
            if (!Schema::hasColumn('coupons', 'min_order_amount')) {
                $table->decimal('min_order_amount', 10, 2)
                    ->nullable()
                    ->after('value');
            }

            // usage_limit ekleyelim
            if (!Schema::hasColumn('coupons', 'usage_limit')) {
                $table->unsignedInteger('usage_limit')
                    ->nullable()
                    ->after('min_order_amount');
            }

            // expires_at ekleyelim
            if (!Schema::hasColumn('coupons', 'expires_at')) {
                $table->timestamp('expires_at')
                    ->nullable()
                    ->after('usage_limit');
            }

            // active (boolean) ekleyelim
            if (!Schema::hasColumn('coupons', 'active')) {
                $table->boolean('active')
                    ->default(true)
                    ->after('expires_at');
            }
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Aşağıda önce foreign key’i düş, sonra sütunu sil
            if (Schema::hasColumn('coupons', 'seller_id')) {
                $table->dropConstrainedForeignId('seller_id');
            }
            if (Schema::hasColumn('coupons', 'min_order_amount')) {
                $table->dropColumn('min_order_amount');
            }
            if (Schema::hasColumn('coupons', 'usage_limit')) {
                $table->dropColumn('usage_limit');
            }
            if (Schema::hasColumn('coupons', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            if (Schema::hasColumn('coupons', 'active')) {
                $table->dropColumn('active');
            }
        });
    }
};

