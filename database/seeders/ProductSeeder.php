<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Products reproduce exactly what is shown in the Shop screenshot
     * (name, price, shape/length/finish, badge). Image filenames are the
     * expected asset names under public/images/products — drop the real
     * photos there using these exact filenames. See README for the full list.
     */
    public function run(): void
    {
        $everyday = Category::where('slug', 'everyday-elegance')->first();
        $softGlam = Category::where('slug', 'soft-glam')->first();
        $boldChic = Category::where('slug', 'bold-chic')->first();
        $luxury = Category::where('slug', 'luxury-edition')->first();

        $products = [
            [
                'name' => 'Rosy Quartz Ombre',
                'price' => 2500,
                'shape' => 'Almond', 'length' => 'Medium', 'finish' => 'Matte',
                'badge' => 'NEW', 'is_best_seller' => true, 'is_featured' => true,
                'category' => $softGlam,
                'image' => 'rosy-quartz-ombre.jpg',
                'gallery' => ['rosy-quartz-ombre.jpg', 'rosy-quartz-ombre-worn.jpg', 'rosy-quartz-ombre-detail.jpg', 'rosy-quartz-ombre-kit.jpg'],
                'short_description' => 'Hand-painted ombre with delicate gold leaf accents.',
                'description' => 'Hand-painted elegance with a seamless gradient and delicate gold leaf accents. Crafted for a salon-quality finish at home. Reusable up to 5 times with proper care.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Midnight Espresso',
                'price' => 2800,
                'shape' => 'Coffin', 'length' => 'Long', 'finish' => 'Glossy',
                'category' => $boldChic,
                'image' => 'midnight-espresso.jpg',
                'gallery' => ['midnight-espresso.jpg'],
                'short_description' => 'Deep espresso tone with a soft gold edge line.',
                'description' => 'A rich, deep espresso shade finished with a delicate soft-gold edge line for a bold, elegant look.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Classic Pearl Mauve',
                'price' => 2200,
                'shape' => 'Almond', 'length' => 'Medium', 'finish' => 'Matte',
                'badge' => 'BEST SELLER', 'is_best_seller' => true, 'is_featured' => true,
                'category' => $everyday,
                'image' => 'classic-pearl-mauve.jpg',
                'gallery' => ['classic-pearl-mauve.jpg'],
                'short_description' => 'A timeless mauve shade, perfect for every day.',
                'description' => 'Our signature everyday mauve — soft, pearlescent, and endlessly wearable.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Ivory Blossom Vine',
                'price' => 3500,
                'shape' => 'Almond', 'length' => 'Medium', 'finish' => 'Matte',
                'category' => $luxury,
                'image' => 'ivory-blossom-vine.jpg',
                'gallery' => ['ivory-blossom-vine.jpg'],
                'short_description' => 'Botanical hand-painted vine detailing on ivory base.',
                'description' => 'Delicate hand-painted florals over an ivory base — romantic and detailed.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Modern French Matte',
                'price' => 2600,
                'shape' => 'Almond', 'length' => 'Medium', 'finish' => 'Matte',
                'category' => $everyday,
                'image' => 'modern-french-matte.jpg',
                'gallery' => ['modern-french-matte.jpg'],
                'short_description' => 'A modern take on the French manicure, in matte mauve.',
                'description' => 'A modern take on the classic French manicure, reimagined in a matte mauve tone.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Gilded Rose Dust',
                'price' => 3200,
                'shape' => 'Coffin', 'length' => 'Medium', 'finish' => 'Glitter/Chrome',
                'badge' => 'LIMITED',
                'category' => $luxury,
                'image' => 'gilded-rose-dust.jpg',
                'gallery' => ['gilded-rose-dust.jpg'],
                'short_description' => 'Blush gold glitter, medium coffin, set of 10.',
                'description' => 'A shimmering blush-gold glitter finish for special occasions and everyday sparkle alike.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Minimalist Dot',
                'price' => 1800,
                'shape' => 'Square', 'length' => 'Short', 'finish' => 'Glossy',
                'category' => $everyday,
                'image' => 'minimalist-dot.jpg',
                'gallery' => ['minimalist-dot.jpg'],
                'short_description' => 'Clean white base with a single accent dot.',
                'description' => 'A clean, minimal white base finished with a single delicate accent dot.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Espresso Split',
                'price' => 2900,
                'shape' => 'Stiletto', 'length' => 'Long', 'finish' => 'Glossy',
                'category' => $boldChic,
                'image' => 'espresso-split.jpg',
                'gallery' => ['espresso-split.jpg'],
                'short_description' => 'Bold espresso and ivory colour-split design.',
                'description' => 'A striking colour-split design pairing deep espresso with warm ivory.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Blush Aura',
                'price' => 3000,
                'shape' => 'Almond', 'length' => 'Long', 'finish' => 'Matte',
                'badge' => 'NEW',
                'category' => $softGlam,
                'image' => 'blush-aura.jpg',
                'gallery' => ['blush-aura.jpg'],
                'short_description' => 'Soft blush-to-nude gradient with a matte finish.',
                'description' => 'A soft, sunset-inspired blush gradient finished with a smooth matte topcoat.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Golden French',
                'price' => 4500,
                'shape' => 'Almond', 'length' => 'Medium', 'finish' => 'Glossy',
                'badge' => 'BEST SELLER', 'is_best_seller' => true,
                'category' => $luxury,
                'image' => 'golden-french.jpg',
                'gallery' => ['golden-french.jpg'],
                'short_description' => 'French tip reimagined with a soft gold line.',
                'description' => 'A modern French manicure finished with a fine soft-gold line along the tip.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Matte Rose',
                'price' => 3800,
                'shape' => 'Coffin', 'length' => 'Long', 'finish' => 'Matte',
                'is_best_seller' => true,
                'category' => $everyday,
                'image' => 'matte-rose.jpg',
                'gallery' => ['matte-rose.jpg'],
                'short_description' => 'A muted matte rose, coffin shape, long length.',
                'description' => 'A muted matte rose shade in a sculpted coffin shape.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
            [
                'name' => 'Pink Marble',
                'price' => 3300,
                'shape' => 'Square', 'length' => 'Medium', 'finish' => 'Glossy',
                'is_best_seller' => true,
                'category' => $boldChic,
                'image' => 'pink-marble.jpg',
                'gallery' => ['pink-marble.jpg'],
                'short_description' => 'Hand-marbled pink and white swirl design.',
                'description' => 'A hand-marbled pink and white swirl, no two sets exactly alike.',
                'whats_included' => ['10 Hand-painted press-on nails', '1 Mini nail file & buffer', '1 Wooden cuticle pusher', '24 Adhesive sticky tabs', '1 Liquid nail glue (2g)', '2 Alcohol prep pads'],
            ],
        ];

        foreach ($products as $data) {
            $category = $data['category'] ?? null;
            unset($data['category']);

            Product::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'category_id' => $category?->id,
                    'sku' => 'ALN-'.strtoupper(Str::random(6)),
                    'stock' => rand(15, 60),
                    'is_best_seller' => $data['is_best_seller'] ?? false,
                    'is_featured' => $data['is_featured'] ?? false,
                ])
            );
        }
    }
}
