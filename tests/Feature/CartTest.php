<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(array $overrides = []): Product
    {
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category-'.Str::random(6)]);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Test Nail Set',
            'slug' => 'test-nail-set-'.Str::random(6),
            'sku' => 'SKU-'.Str::random(6),
            'price' => 2000,
            'stock' => 5,
            'is_active' => true,
        ], $overrides));
    }

    public function test_a_product_can_be_added_to_the_cart(): void
    {
        $product = $this->makeProduct();

        $response = $this->post(route('cart.add', $product), ['qty' => 2]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(2, session('alishe_cart') ? array_sum(array_column(session('alishe_cart'), 'qty')) : 0);
    }

    public function test_cannot_add_more_than_available_stock(): void
    {
        $product = $this->makeProduct(['stock' => 3]);

        $response = $this->post(route('cart.add', $product), ['qty' => 10]);

        $response->assertSessionHas('error');
        $cart = session('alishe_cart', []);
        $this->assertEmpty($cart, 'Cart should not contain an item that exceeds available stock.');
    }

    public function test_out_of_stock_product_cannot_be_added(): void
    {
        $product = $this->makeProduct(['stock' => 0]);

        $response = $this->post(route('cart.add', $product), ['qty' => 1]);

        $response->assertSessionHas('error');
        $this->assertEmpty(session('alishe_cart', []));
    }

    public function test_cart_quantity_is_capped_at_current_stock_on_update(): void
    {
        $product = $this->makeProduct(['stock' => 5]);
        $this->post(route('cart.add', $product), ['qty' => 2]);

        $cart = session('alishe_cart');
        $rowId = array_key_first($cart);

        // Stock drops to 1 after the item was added (e.g. another order came in).
        $product->update(['stock' => 1]);

        $this->patch(route('cart.update', $rowId), ['qty' => 5]);

        $cart = session('alishe_cart');
        $this->assertEquals(1, $cart[$rowId]['qty'], 'Quantity must never exceed live stock, even if requested higher.');
    }
}
