<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_page_only_shows_active_products(): void
    {
        $activeProduct = Product::create([
            'name' => 'Active Nails',
            'sku' => 'ALN-ACT-1',
            'price' => 2500,
            'stock' => 10,
            'is_active' => true,
            'slug' => 'active-nails',
        ]);

        $inactiveProduct = Product::create([
            'name' => 'Inactive Nails',
            'sku' => 'ALN-INA-1',
            'price' => 2500,
            'stock' => 10,
            'is_active' => false,
            'slug' => 'inactive-nails',
        ]);

        $response = $this->get(route('shop.index'));

        $response->assertStatus(200);
        $response->assertSee($activeProduct->name);
        $response->assertDontSee($inactiveProduct->name);
    }

    public function test_shop_page_filters_by_category(): void
    {
        $category1 = Category::create(['name' => 'Soft Glam', 'slug' => 'soft-glam']);
        $category2 = Category::create(['name' => 'Bold & Chic', 'slug' => 'bold-chic']);

        $product1 = Product::create([
            'name' => 'Glam Set',
            'category_id' => $category1->id,
            'sku' => 'ALN-GLM-1',
            'price' => 2500,
            'stock' => 10,
            'is_active' => true,
            'slug' => 'glam-set',
        ]);

        $product2 = Product::create([
            'name' => 'Bold Set',
            'category_id' => $category2->id,
            'sku' => 'ALN-BLD-1',
            'price' => 2500,
            'stock' => 10,
            'is_active' => true,
            'slug' => 'bold-set',
        ]);

        $response = $this->get(route('shop.index', ['category' => 'soft-glam']));

        $response->assertStatus(200);
        $response->assertSee($product1->name);
        $response->assertDontSee($product2->name);
    }

    public function test_inactive_product_detail_page_returns_404(): void
    {
        $inactiveProduct = Product::create([
            'name' => 'Hidden Nails',
            'sku' => 'ALN-HDN-1',
            'price' => 2500,
            'stock' => 10,
            'is_active' => false,
            'slug' => 'hidden-nails',
        ]);

        $response = $this->get(route('products.show', $inactiveProduct));

        $response->assertStatus(404);
    }
}
