<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_product_names_receive_unique_slugs(): void
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        // Build a fresh payload per request — a moved UploadedFile's temp
        // file is consumed by the first save, so it can't be reused.
        $payload = fn () => [
            'name' => 'Classic Set',
            'category_id' => $category->id,
            'price' => 1500,
            'stock' => 5,
            'is_active' => '1',
            // At least one media item is required for every product.
            'media_files' => [UploadedFile::fake()->createWithContent('cover.png', $png)],
            'media_types' => ['image'],
        ];

        $this->actingAs($admin, 'admin')->post(route('admin.products.store'), $payload())->assertRedirect();
        $this->actingAs($admin, 'admin')->post(route('admin.products.store'), array_merge($payload(), [
            'sku' => 'ignored',
        ]))->assertRedirect();

        $this->assertDatabaseHas('products', ['slug' => 'classic-set']);
        $this->assertDatabaseHas('products', ['slug' => 'classic-set-2']);

        foreach (Product::whereIn('slug', ['classic-set', 'classic-set-2'])->get() as $product) {
            @unlink(public_path('images/products/'.$product->media[0]['path']));
        }
    }

    public function test_admin_can_save_an_ordered_mixed_media_gallery(): void
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'gallery-admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);
        $category = Category::create(['name' => 'Media Category', 'slug' => 'media-category']);
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        // Minimal bytes carrying an MP4 `ftyp` box so finfo detects video/mp4.
        $mp4 = base64_decode('AAAAIGZ0eXBpc29tAAACAGlzb21pc28yYXZjMW1wNDE=');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), [
                'name' => 'Media Set',
                'category_id' => $category->id,
                'price' => 1800,
                'stock' => 5,
                'is_active' => '1',
                // Freely interleaved: new image at 0, retained upload at 1,
                // new video at 2 — the array keys carry the position.
                'media_files' => [
                    0 => UploadedFile::fake()->createWithContent('front.png', $png),
                    2 => UploadedFile::fake()->createWithContent('clip.mp4', $mp4),
                ],
                'media_types' => [
                    0 => 'image',
                    2 => 'video',
                ],
                'existing_media' => [
                    1 => 'image|existing-cover.jpg',
                ],
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $product = Product::where('slug', 'media-set')->firstOrFail();
        $media = $product->media;

        $this->assertCount(3, $media);
        $this->assertSame(['image', 'image', 'video'], array_column($media, 'type'));
        $this->assertSame([0, 1, 2], array_column($media, 'order'));
        $this->assertSame('existing-cover.jpg', $media[1]['path']);

        $this->assertFileExists(public_path('images/products/'.$media[0]['path']));
        $this->assertFileExists(public_path('videos/products/'.$media[2]['path']));
        @unlink(public_path('images/products/'.$media[0]['path']));
        @unlink(public_path('videos/products/'.$media[2]['path']));
    }

    public function test_product_media_requires_at_least_one_item(): void
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'required-media@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);
        $category = Category::create(['name' => 'Required Category', 'slug' => 'required-category']);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), [
                'name' => 'No Media Set',
                'category_id' => $category->id,
                'price' => 1500,
                'stock' => 5,
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('media');

        $this->assertDatabaseMissing('products', ['slug' => 'no-media-set']);
    }
}