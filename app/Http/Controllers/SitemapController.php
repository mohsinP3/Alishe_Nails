<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('shop.index'), 'priority' => '0.9'],
            ['loc' => route('about.index'), 'priority' => '0.6'],
            ['loc' => route('how-to-apply.index'), 'priority' => '0.6'],
            ['loc' => route('policies.index'), 'priority' => '0.4'],
            ['loc' => route('contact.index'), 'priority' => '0.5'],
        ])->merge(
            Product::active()->get(['slug', 'updated_at'])->map(fn (Product $product) => [
                'loc' => route('products.show', $product),
                'lastmod' => optional($product->updated_at)->toAtomString(),
                'priority' => '0.8',
            ])
        );

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}