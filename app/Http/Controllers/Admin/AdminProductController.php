<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->paginate(15)->withQueryString();

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

    public function store(ProductRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = $this->uniqueSlug($validated['name']);
        $validated['sku'] = 'ALN-'.strtoupper(Str::random(6));
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

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

    public function update(ProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        $validated['slug'] = $this->uniqueSlug($validated['name'], $product);
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

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
     * Saves the uploaded image straight into public/images/products.
     * The filename is fully regenerated (slug + timestamp + validated
     * extension) — the client-supplied original filename is never trusted
     * or used directly, which rules out path traversal / double-extension
     * tricks. ProductRequest's `image`+`mimes` rules already reject
     * non-image / executable uploads before this ever runs.
     */
    private function storeImage(Request $request): string
    {
        $file = $request->file('image');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'-'.time().'-'.Str::random(6).'.'.$extension;

        $file->move(public_path('images/products'), $filename);

        return $filename;
    }

    private function uniqueSlug(string $name, ?Product $ignore = null): string
    {
        $baseSlug = Str::slug($name) ?: 'product';
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::where('slug', $slug)
            ->when($ignore, fn ($query) => $query->where('id', '!=', $ignore->id))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }
}
