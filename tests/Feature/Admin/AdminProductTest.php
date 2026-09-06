<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
}