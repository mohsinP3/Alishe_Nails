<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'seller_id')) {
                $table->unsignedBigInteger('seller_id')->nullable()->after('product_id')->index();
            }
            $table->foreign('seller_id', 'order_items_seller_id_foreign')->references('id')->on('sellers')->nullOnDelete();
            $table->decimal('commission_rate', 5, 2)->nullable()->after('line_total');
            $table->decimal('commission_amount', 10, 2)->nullable()->after('commission_rate');
            $table->decimal('seller_earning', 10, 2)->nullable()->after('commission_amount');
            $table->enum('payout_status', ['pending', 'paid'])->default('pending')->after('seller_earning')->index();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('order_items_seller_id_foreign');
            $table->dropColumn(['commission_rate', 'commission_amount', 'seller_earning', 'payout_status']);
            $table->dropColumn('seller_id');
        });
    }
};
