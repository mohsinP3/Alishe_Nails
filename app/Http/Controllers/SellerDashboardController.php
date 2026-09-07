<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SellerDashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::guard('seller')->user();
        $seller->load('activeSubscription.plan');
        $products = $seller->products()->latest()->paginate(12);
        $items = $seller->orderItems()->with('order')->latest()->paginate(12, ['*'], 'orders_page');
        $sales = (float) $seller->orderItems()->sum('line_total');
        $earnings = (float) $seller->orderItems()->sum('line_total');
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('duration_days')->get();

        return view('seller.dashboard', compact('seller', 'products', 'items', 'sales', 'earnings', 'plans'));
    }

    public function createProduct()
    {
        return view('seller.product-form', ['product' => new Product, 'categories' => Category::all()]);
    }

    public function storeProduct(SellerProductRequest $request)
    {
        $seller = Auth::guard('seller')->user();
        $data = $this->productData($request);
        $data['seller_id'] = $seller->id;
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['sku'] = 'SEL-'.strtoupper(Str::random(7));
        Product::create($data);
        return redirect()->route('seller.dashboard')->with('success', 'Product submitted and is now live in your shop.');
    }

    public function editProduct(Product $product)
    {
        $this->ensureOwner($product);
        return view('seller.product-form', ['product' => $product, 'categories' => Category::all()]);
    }

    public function updateProduct(SellerProductRequest $request, Product $product)
    {
        $this->ensureOwner($product);
        $product->update($this->productData($request));
        return redirect()->route('seller.dashboard')->with('success', 'Product updated.');
    }

    public function destroyProduct(Product $product)
    {
        $this->ensureOwner($product);
        $product->update(['is_active' => false]);
        return back()->with('success', 'Product hidden from the shop.');
    }

    private function ensureOwner(Product $product): void
    {
        abort_unless($product->seller_id === Auth::guard('seller')->id(), 403);
    }

    private function productData(SellerProductRequest $request): array
    {
        $data = $request->validated();
        $data['is_active'] = true;
        if ($request->hasFile('image')) $data['image'] = $this->storeImage($request->file('image'));
        unset($data['gallery']);
        if ($request->hasFile('gallery')) {
            $data['gallery'] = collect($request->file('gallery'))->map(fn ($file) => $this->storeImage($file))->all();
        }
        return $data;
    }

    private function storeImage($file): string
    {
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'-'.time().'-'.Str::random(6).'.'.strtolower($file->getClientOriginalExtension());

        if (! is_dir(public_path('images/products'))) {
            mkdir(public_path('images/products'), 0775, true);
        }

        $file->move(public_path('images/products'), $filename);

        return $filename;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'seller-product';
        $slug = $base;
        $suffix = 2;
        while (Product::where('slug', $slug)->exists()) $slug = $base.'-'.($suffix++);
        return $slug;
    }
}
