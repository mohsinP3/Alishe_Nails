<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('shape')->nullable();     // Almond, Coffin, Square, Stiletto
            $table->string('length')->nullable();     // Short, Medium, Long, Extra Long
            $table->string('finish')->nullable();      // Glossy, Matte, Glitter/Chrome
            $table->string('image')->nullable();       // filename inside public/images/products
            $table->json('gallery')->nullable();       // array of filenames
            $table->string('badge')->nullable();        // NEW, BEST SELLER, LIMITED
            $table->boolean('is_best_seller')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('stock')->default(0);
            $table->json('whats_included')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
