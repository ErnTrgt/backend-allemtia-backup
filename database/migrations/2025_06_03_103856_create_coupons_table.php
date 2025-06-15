<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            // Kupon kodu (örneğin: SUMMER20), benzersiz olsun

            $table->enum('type', ['percent', 'fixed']);
            // 'percent' => yüzde indirim, 'fixed' => sabit tutar indirim

            $table->decimal('value', 10, 2);
            // Örneğin yüzde indirimse 20.00, sabit tutar indirimse 50.00 gibi

            $table->decimal('min_order_amount', 10, 2)->nullable();
            // Eğer “kuponu kullanmak için minimum sepet tutarı” şartı varsa buraya yazılır. 
            // Yoksa null bırakabilirsiniz.

            $table->unsignedInteger('usage_limit')->nullable();
            // Kuponun kaç kez kullanılabileceği (null ise sınırsız/isteğe bağlı kontrol yaparsınız)

            $table->dateTime('expires_at')->nullable();
            // Kuponun geçerlilik bitiş tarihi (örneğin '2025-12-31 23:59:59')

            $table->unsignedInteger('used_count')->default(0);
            // Bu kupon şu ana kadar kaç kez kullanıldı (kullanıldığında +1 artacak)

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
