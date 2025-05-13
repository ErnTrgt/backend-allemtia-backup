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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('tag_one')->nullable();     // ALLEMTIA gibi
            $table->string('tag_two')->nullable();     // Başlık
            $table->text('description')->nullable();   // Açıklama
            $table->string('image')->nullable();       // Görsel yolu
            $table->boolean('status')->default(1);     // Aktif/Pasif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
