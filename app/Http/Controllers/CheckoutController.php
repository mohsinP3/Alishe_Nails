<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Mail\AdminNewOrderMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if (empty(Cart::content())) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = Cart::subtotal();
        $shipping = ShippingRate::calculateFee(
            old('city', 'Karachi'),
            old('area'),
            $subtotal
        )['fee'];

        return view('checkout.index', [
            'items' => Cart::content(),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
            'user' => $request->user(),
        ]);
    }

    public function calculateShippingFee(Request $request)
    {
        $city = $request->string('city')->trim()->toString() ?: 'Karachi';
        $area = $request->string('area')->trim()->toString() ?: null;
        $subtotal = Cart::subtotal();

        $res = ShippingRate::calculateFee($city, $area, $subtotal);
        $total = $subtotal + $res['fee'];

        return response()->json([
            'subtotal' => $subtotal,
            'shipping' => $res['fee'],
            'standard_fee' => $res['standard_fee'],
            'threshold' => $res['threshold'],
            'is_free' => $res['is_free'],
            'total' => $total,
            'zone_label' => $res['zone_label'],
        ]);
    }

    public function store(StoreCheckoutRequest $request)
    {
        $cartContent = Cart::content();

        if (empty($cartContent)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validated();

        // Prevent double-submit (double-click / form resubmission)
        $cartSignature = md5(json_encode($cartContent).$request->user()?->id);
        $lastSignature = $request->session()->get('last_order_signature');
        $lastSignatureAt = $request->session()->get('last_order_signature_at');

        if ($lastSignature === $cartSignature && $lastSignatureAt && now()->diffInSeconds($lastSignatureAt) < 30) {
            $existingOrder = Order::where('order_number', $request->session()->get('last_order_number'))->first();

            if ($existingOrder) {
                return redirect()->route('checkout.success', [
                    'order' => $existingOrder,
                    'signature' => $existingOrder->access_token,
                ]);
            }
        }

        try {
            $order = DB::transaction(function () use ($validated, $cartContent, $request) {
                $subtotal = 0;
                $lineItems = [];

                foreach ($cartContent as $row) {
                    $product = Product::where('id', $row['product_id'])->lockForUpdate()->first();

                    if (! $product || ! $product->is_active) {
                        throw ValidationException::withMessages([
                            'cart' => "\"{$row['name']}\" is no longer available. Please remove it from your cart.",
                        ]);
                    }

                    if ($product->stock < $row['qty']) {
                        throw ValidationException::withMessages([
                            'cart' => "Only {$product->stock} left of \"{$product->name}\" — please update the quantity in your cart.",
                        ]);
                    }

                    $currentPrice = (float) $product->price;
                    $lineTotal = $currentPrice * $row['qty'];
                    $subtotal += $lineTotal;

                    $lineItems[] = [
                        'product' => $product,
                        'name' => $product->name,
                        'shape' => $row['shape'],
                        'size' => $row['size'],
                        'qty' => $row['qty'],
                        'price' => $currentPrice,
                        'line_total' => $lineTotal,
                    ];

                    $product->decrement('stock', $row['qty']);
                }

                $shippingRes = ShippingRate::calculateFee(
                    $validated['city'],
                    $validated['area'] ?? null,
                    $subtotal
                );
                $shipping = $shippingRes['fee'];

                $order = Order::create([
                    ...$validated,
                    'user_id' => $request->user()?->id,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $subtotal + $shipping,
                ]);

                foreach ($lineItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'product_name' => $item['name'],
                        'shape' => $item['shape'],
                        'size' => $item['size'],
                        'quantity' => $item['qty'],
                        'price' => $item['price'],
                        'line_total' => $item['line_total'],
                    ]);
                }

                return $order;
            });
        } catch (ValidationException $e) {
            return redirect()->route('cart.index')->withErrors($e->errors());
        }

        Cart::clear();

        $request->session()->put('last_order_signature', $cartSignature);
        $request->session()->put('last_order_signature_at', now());
        $request->session()->put('last_order_number', $order->order_number);

        // Order confirmation email — failure here must never break checkout,
        // so it's caught and logged instead of bubbling up to the customer.
        try {
            if ($order->email) {
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
            }
            Mail::to(config('services.admin.notification_email'))->send(new AdminNewOrderMail($order));
        } catch (\Throwable $e) {
            Log::warning('Order notification email failed: '.$e->getMessage());
        }

        return redirect()->route('checkout.success', [
            'order' => $order,
            'signature' => $order->access_token,
        ])->with('success', 'Order placed successfully!');
    }

    /**
     * Guests view their confirmation via the unguessable access_token
     * (?signature=...) generated at order creation. Logged-in customers can
     * also view it if the order belongs to them (OrderPolicy). Sequential
     * IDs alone (e.g. /checkout/success/2) are never sufficient.
     */
    public function success(Request $request, Order $order)
    {
        $validToken = $request->query('signature') === $order->access_token;
        $isOwner = $request->user() && $order->user_id === $request->user()->id;

        if (! $validToken && ! $isOwner) {
            abort(403, 'You are not authorized to view this order.');
        }

        $order->load('items');

        return view('checkout.success', compact('order'));
    }
}
