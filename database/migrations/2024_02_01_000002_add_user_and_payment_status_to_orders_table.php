<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds customer ownership + payment-status tracking to orders.
     * user_id is nullable so guest checkout keeps working; when a logged-in
     * customer checks out, the order is automatically linked to their account.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('payment_status')->default('pending')->after('status'); // pending, paid, failed, cancelled
            $table->string('access_token', 64)->nullable()->unique()->after('order_number');
            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['payment_status', 'access_token']);
            $table->dropIndex(['status']);
            $table->dropIndex(['email']);
        });
    }
};
