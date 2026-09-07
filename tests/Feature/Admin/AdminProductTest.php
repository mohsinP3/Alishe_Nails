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
        $payload = [
            'name' => 'Classic Set',
            'category_id' => $category->id,
            'price' => 1500,
            'stock' => 5,
            'is_active' => '1',
        ];

        $this->actingAs($admin, 'admin')->post(route('admin.products.store'), $payload)->assertRedirect();
        $this->actingAs($admin, 'admin')->post(route('admin.products.store'), array_merge($payload, [
            'sku' => 'ignored',
        ]))->assertRedirect();

        $this->assertDatabaseHas('products', ['slug' => 'classic-set']);
        $this->assertDatabaseHas('products', ['slug' => 'classic-set-2']);
    }

    public function test_admin_can_upload_multiple_gallery_images_for_a_product(): void
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'gallery-admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);
        $category = Category::create(['name' => 'Gallery Category', 'slug' => 'gallery-category']);
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        $files = [
            UploadedFile::fake()->createWithContent('front.png', $png),
            UploadedFile::fake()->createWithContent('detail.png', $png),
            UploadedFile::fake()->createWithContent('packaging.png', $png),
        ];

        $this->actingAs($admin, 'admin')
            ->post(route('admin.products.store'), [
                'name' => 'Gallery Set',
                'category_id' => $category->id,
                'price' => 1800,
                'stock' => 5,
                'is_active' => '1',
                'gallery' => $files,
            ])
            ->assertRedirect();

        $product = Product::where('slug', 'gallery-set')->firstOrFail();

        $this->assertCount(3, $product->gallery);
        foreach ($product->gallery as $filename) {
            $this->assertFileExists(public_path('images/products/'.$filename));
            @unlink(public_path('images/products/'.$filename));
        }
    }
}