<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('steam_app_id')->unique();
            $table->string('name');
            $table->string('header_image_url')->nullable();
            $table->decimal('current_price', 10, 2)->nullable();
            $table->unsignedInteger('current_discount_percent')->default(0);
            $table->boolean('is_free')->default(false);
            $table->timestamp('price_last_checked_at')->nullable();
            $table->timestamps();

            $table->index('steam_app_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
