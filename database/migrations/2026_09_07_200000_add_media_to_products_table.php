<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the unified ordered `media` gallery to products and backfills it
     * from the legacy `image` + `gallery` columns so existing products keep
     * rendering. The legacy columns stay in the table (no longer written to)
     * for backward compatibility.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('media')->nullable()->after('gallery');
        });

        Product::query()
            ->whereNull('media')
            ->chunkById(100, function ($products) {
                foreach ($products as $product) {
                    $media = [];

                    if (! empty($product->image)) {
                        $media[] = ['type' => 'image', 'path' => $product->image, 'order' => 0];
                    }

                    foreach ((array) ($product->gallery ?? []) as $path) {
                        $media[] = ['type' => 'image', 'path' => $path, 'order' => count($media)];
                    }

                    if ($media !== []) {
                        $product->forceFill(['media' => $media])->save();
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('media');
        });
    }
};