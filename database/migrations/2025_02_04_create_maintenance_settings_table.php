<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenance_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('title')->default('Site Bakımda');
            $table->text('message')->nullable();
            $table->datetime('estimated_end_time')->nullable();
            $table->json('allowed_ips')->nullable();
            $table->enum('template_type', ['simple', 'detailed', 'custom'])->default('simple');
            $table->json('notify_emails')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_settings');
    }
};