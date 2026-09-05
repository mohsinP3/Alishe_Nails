<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'area',
        'delivery_fee',
        'free_shipping_threshold',
        'is_active',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Calculates the exact location-based shipping fee and free shipping threshold.
     */
    public static function calculateFee(string $city, ?string $area, float $subtotal): array
    {
        $city = trim($city);
        $area = $area ? trim($area) : null;

        $rate = null;
        if ($area) {
            $rate = self::where('is_active', true)
                ->where('city', 'like', $city)
                ->where('area', 'like', $area)
                ->first();
        }

        if (! $rate) {
            $rate = self::where('is_active', true)
                ->where('city', 'like', $city)
                ->where(function ($q) {
                    $q->whereNull('area')->orWhere('area', '');
                })
                ->first();
        }

        if (! $rate) {
            $rate = self::where('is_active', true)
                ->where('city', 'Other Cities')
                ->first();
        }

        $standardFee = $rate ? (float) $rate->delivery_fee : 250.00;
        $threshold = ($rate && $rate->free_shipping_threshold !== null)
            ? (float) $rate->free_shipping_threshold
            : 5000.00;

        $isFree = $subtotal > 0 && $subtotal >= $threshold;
        $finalFee = $isFree ? 0.00 : $standardFee;

        return [
            'fee' => $finalFee,
            'standard_fee' => $standardFee,
            'threshold' => $threshold,
            'is_free' => $isFree,
            'zone_label' => $rate ? ($rate->area ? "{$rate->city} ({$rate->area})" : $rate->city) : $city,
        ];
    }
}
