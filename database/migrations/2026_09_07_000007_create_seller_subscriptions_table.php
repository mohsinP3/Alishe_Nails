<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->restrictOnDelete();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('duration_days');
            $table->enum('status', ['pending', 'active', 'rejected', 'expired'])->default('pending')->index();
            $table->enum('payment_method', ['cod', 'bank_transfer', 'jazzcash_easypaisa']);
            $table->string('transaction_reference')->nullable();
            $table->text('payment_notes')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_subscriptions');
    }
};
