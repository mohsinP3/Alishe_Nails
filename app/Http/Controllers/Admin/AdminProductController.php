<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.products.form', [
            'product' => new Product,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['sku'] = 'ALN-'.strtoupper(Str::random(6));
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeImage($request);
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product added.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validated($request, $product->id);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeImage($request);
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product deleted.');
    }

    /**
     * Shared validation for store/update. Never trust the client — every
     * field is re-validated here regardless of what the form already checked.
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'shape' => ['nullable', 'string', 'max:50'],
            'length' => ['nullable', 'string', 'max:50'],
            'finish' => ['nullable', 'string', 'max:50'],
            'badge' => ['nullable', 'string', 'max:30'],
            'is_best_seller' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    /**
     * Saves the uploaded image straight into public/images/products so it
     * works with the existing Product::image_url accessor without needing
     * `php artisan storage:link`.
     */
    private function storeImage(Request $request): string
    {
        $file = $request->file('image');
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'-'.time().'.'.$file->getClientOriginalExtension();

        $file->move(public_path('images/products'), $filename);

        return $filename;
    }
}
