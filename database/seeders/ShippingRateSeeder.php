<?php

namespace Database\Seeders;

use App\Models\ShippingRate;
use Illuminate\Database\Seeder;

class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['city' => 'Karachi', 'area' => 'Korangi / Korangi 2 1/2', 'delivery_fee' => 150, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'Landhi', 'delivery_fee' => 150, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'Shah Faisal Colony', 'delivery_fee' => 150, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'Malir', 'delivery_fee' => 180, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'Gulshan', 'delivery_fee' => 180, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'PECHS', 'delivery_fee' => 180, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'DHA', 'delivery_fee' => 200, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'Clifton', 'delivery_fee' => 200, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'Nazimabad / North Nazimabad', 'delivery_fee' => 200, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'Orangi', 'delivery_fee' => 220, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => 'Saddar', 'delivery_fee' => 180, 'free_shipping_threshold' => 5000],
            ['city' => 'Karachi', 'area' => null, 'delivery_fee' => 200, 'free_shipping_threshold' => 5000],
            ['city' => 'Lahore', 'area' => null, 'delivery_fee' => 250, 'free_shipping_threshold' => 5000],
            ['city' => 'Islamabad', 'area' => null, 'delivery_fee' => 250, 'free_shipping_threshold' => 5000],
            ['city' => 'Rawalpindi', 'area' => null, 'delivery_fee' => 250, 'free_shipping_threshold' => 5000],
            ['city' => 'Other Cities', 'area' => null, 'delivery_fee' => 250, 'free_shipping_threshold' => 5000],
        ];

        foreach ($rates as $rate) {
            ShippingRate::firstOrCreate(
                ['city' => $rate['city'], 'area' => $rate['area']],
                [
                    'delivery_fee' => $rate['delivery_fee'],
                    'free_shipping_threshold' => $rate['free_shipping_threshold'],
                    'is_active' => true,
                ]
            );
        }
    }
}
