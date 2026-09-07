<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_policies_page_displays_returns_and_privacy_sections(): void
    {
        $response = $this->get(route('policies.index'));

        $response->assertOk()
            ->assertSee('No Returns or Exchanges')
            ->assertSee('Privacy Policy');
    }

    public function test_sitemap_is_available_as_xml(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<urlset', false)
            ->assertSee(route('shop.index'), false);
    }

    public function test_product_page_contains_open_graph_metadata(): void
    {
        $product = \App\Models\Product::create([
            'name' => 'SEO Test Set',
            'slug' => 'seo-test-set',
            'sku' => 'SEO-TEST-1',
            'price' => 2000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('property="og:title"', false)
            ->assertSee('SEO Test Set — Alishe Nails', false)
            ->assertSee('property="og:image"', false);
    }
}
