<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $sample = [
            ['name' => 'Ayesha K.', 'rating' => 5, 'comment' => 'Fit perfectly and lasted almost two weeks. Will reorder!'],
            ['name' => 'Fatima R.', 'rating' => 4, 'comment' => 'Beautiful finish, application was easy with the included kit.'],
            ['name' => 'Sana M.', 'rating' => 5, 'comment' => 'Exactly like the photos. So many compliments!'],
        ];

        Product::all()->each(function (Product $product) use ($sample) {
            foreach ($sample as $review) {
                Review::firstOrCreate([
                    'product_id' => $product->id,
                    'customer_name' => $review['name'],
                ], [
                    'rating' => $review['rating'],
                    'comment' => $review['comment'],
                ]);
            }
        });
    }
}
