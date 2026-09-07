<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductMediaTest extends TestCase
{
    use RefreshDatabase;

    private function pngBytes(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
    }

    public function test_media_items_fall_back_to_legacy_image_and_gallery_columns(): void
    {
        $product = Product::create([
            'name' => 'Legacy Set',
            'slug' => 'legacy-set',
            'sku' => 'ALN-LEGACY',
            'price' => 1000,
            'stock' => 10,
            'is_active' => true,
            'image' => 'legacy-cover.jpg',
            'gallery' => ['legacy-1.jpg', 'legacy-2.jpg'],
        ]);

        $this->assertSame([
            ['type' => 'image', 'path' => 'legacy-cover.jpg', 'order' => 0],
            ['type' => 'image', 'path' => 'legacy-1.jpg', 'order' => 1],
            ['type' => 'image', 'path' => 'legacy-2.jpg', 'order' => 2],
        ], $product->media_items);
    }

    public function test_product_page_renders_mixed_video_and_image_media_in_order(): void
    {
        if (! is_dir(public_path('images/products'))) {
            mkdir(public_path('images/products'), 0775, true);
        }
        if (! is_dir(public_path('videos/products'))) {
            mkdir(public_path('videos/products'), 0775, true);
        }

        file_put_contents(public_path('images/products/test-media-cover.png'), $this->pngBytes());
        file_put_contents(public_path('videos/products/test-media-clip.mp4'), 'dummy-bytes');

        $product = Product::create([
            'name' => 'Video Set',
            'slug' => 'video-set',
            'sku' => 'ALN-VIDEO',
            'price' => 1200,
            'stock' => 5,
            'is_active' => true,
            'media' => [
                ['type' => 'video', 'path' => 'test-media-clip.mp4', 'order' => 0],
                ['type' => 'image', 'path' => 'test-media-cover.png', 'order' => 1],
            ],
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        // The video is first in the ordered gallery, so a <video> element is
        // rendered; the image follows as the second slide and thumbnail.
        $response->assertSee('<video', false);
        $response->assertSee('videos/products/test-media-clip.mp4', false);
        $response->assertSee('images/products/test-media-cover.png', false);

        // Exactly one gallery slot + one thumbnail per ACTUAL media item —
        // never an empty placeholder slot.
        $this->assertSame(2, substr_count($response->getContent(), 'data-media-slide'));
        $this->assertSame(2, substr_count($response->getContent(), 'data-media-thumb'));

        @unlink(public_path('images/products/test-media-cover.png'));
        @unlink(public_path('videos/products/test-media-clip.mp4'));
    }

    public function test_product_page_with_a_single_image_renders_exactly_one_slot(): void
    {
        if (! is_dir(public_path('images/products'))) {
            mkdir(public_path('images/products'), 0775, true);
        }

        file_put_contents(public_path('images/products/test-single.png'), $this->pngBytes());

        $product = Product::create([
            'name' => 'Single Set',
            'slug' => 'single-set',
            'sku' => 'ALN-SINGLE',
            'price' => 900,
            'stock' => 5,
            'is_active' => true,
            'media' => [
                ['type' => 'image', 'path' => 'test-single.png', 'order' => 0],
            ],
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $html = $response->getContent();

        // BUG FIX REGRESSION: one image = one gallery slot, no second
        // placeholder slot, and no thumbnail strip at all.
        $this->assertSame(1, substr_count($html, 'data-media-slide'));
        $this->assertStringNotContainsString('No image', $html);
        $this->assertStringNotContainsString('product-gallery__thumbs', $html);

        @unlink(public_path('images/products/test-single.png'));
    }

    public function test_shop_cards_render_a_video_cover_as_a_video_element(): void
    {
        if (! is_dir(public_path('videos/products'))) {
            mkdir(public_path('videos/products'), 0775, true);
        }

        file_put_contents(public_path('videos/products/test-card-clip.mp4'), 'dummy-bytes');

        Product::create([
            'name' => 'Card Video Set',
            'slug' => 'card-video-set',
            'sku' => 'ALN-CARD',
            'price' => 1400,
            'stock' => 5,
            'is_active' => true,
            'media' => [
                ['type' => 'video', 'path' => 'test-card-clip.mp4', 'order' => 0],
            ],
        ]);

        $response = $this->get(route('shop.index'));

        $response->assertOk();
        $response->assertSee('<video', false);

        @unlink(public_path('videos/products/test-card-clip.mp4'));
    }
}