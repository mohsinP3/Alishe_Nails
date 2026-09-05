<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\Request;

class AdminShippingController extends Controller
{
    public function index(Request $request)
    {
        $query = ShippingRate::query()->orderBy('city')->orderBy('area');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('city', 'like', "%{$search}%")
                    ->orWhere('area', 'like', "%{$search}%");
            });
        }

        $shippingRates = $query->paginate(15)->withQueryString();

        return view('admin.shipping.index', compact('shippingRates'));
    }

    public function create()
    {
        return view('admin.shipping.form', ['shipping' => new ShippingRate]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:150'],
            'delivery_fee' => ['required', 'numeric', 'min:0', 'max:99999'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        ShippingRate::create($validated);

        return redirect()->route('admin.shipping.index')->with('success', 'Shipping rate created.');
    }

    public function edit(ShippingRate $shipping)
    {
        return view('admin.shipping.form', compact('shipping'));
    }

    public function update(Request $request, ShippingRate $shipping)
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:150'],
            'delivery_fee' => ['required', 'numeric', 'min:0', 'max:99999'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $shipping->update($validated);

        return redirect()->route('admin.shipping.index')->with('success', 'Shipping rate updated.');
    }

    public function destroy(ShippingRate $shipping)
    {
        $shipping->delete();

        return back()->with('success', 'Shipping rate deleted.');
    }
}
