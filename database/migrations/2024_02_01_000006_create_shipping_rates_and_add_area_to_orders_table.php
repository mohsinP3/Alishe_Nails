<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('area')->nullable();
            $table->decimal('delivery_fee', 10, 2);
            $table->decimal('free_shipping_threshold', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['city', 'area']);
            $table->index('is_active');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('area')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('area');
            $table->string('transaction_reference')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['area', 'postal_code', 'transaction_reference']);
        });
    }
};
