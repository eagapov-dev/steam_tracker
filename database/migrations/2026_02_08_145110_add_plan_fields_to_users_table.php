<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('plan')->default('free')->after('password');
            $table->string('lemon_squeezy_customer_id')->nullable()->after('plan');
            $table->string('subscription_status')->nullable()->after('lemon_squeezy_customer_id');
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_status');
            $table->string('telegram_chat_id')->nullable()->after('subscription_ends_at');
            $table->boolean('is_admin')->default(false)->after('telegram_chat_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'plan', 'lemon_squeezy_customer_id', 'subscription_status',
                'subscription_ends_at', 'telegram_chat_id', 'is_admin',
            ]);
        });
    }
};
