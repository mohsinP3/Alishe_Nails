<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->boolean('is_approved')->default(false)->after('comment');
            $table->boolean('is_verified_purchase')->default(false)->after('is_approved');
            $table->unique(['product_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'user_id']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['is_approved', 'is_verified_purchase']);
        });
    }
};
