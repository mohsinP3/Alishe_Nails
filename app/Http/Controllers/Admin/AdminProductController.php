<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
        $validated = $this->validatedProductData($request);

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
        $validated = $this->validatedProductData($request, $product);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product deleted.');
    }

    private function validatedProductData(ProductRequest $request, ?Product $product = null): array
    {
        $validated = $request->validated();

        $validated['slug'] = $this->uniqueSlug($validated['name'], $product);
        $validated['sku'] = $validated['sku'] ?? 'ALN-'.strtoupper(Str::random(6));
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $product === null
            ? ($request->has('is_active') ? $request->boolean('is_active') : true)
            : $request->boolean('is_active');

        // Persist the ordered mixed media gallery (see buildOrderedMedia).
        $validated['media'] = $this->buildOrderedMedia($request);

        unset($validated['existing_media'], $validated['media_files'], $validated['media_types']);

        return $validated;
    }

    /**
     * Merges the already-uploaded items the admin kept ("type|path" packed
     * strings) with the newly picked files into ONE ordered gallery — the
     * array keys carry each item's position, so images and videos can be
     * freely interleaved. Returns [{ type, path, order }, ...].
     */
    private function buildOrderedMedia(ProductRequest $request): array
    {
        $items = [];

        foreach ($request->input('existing_media', []) as $position => $packed) {
            if (! is_string($packed) || ! str_contains($packed, '|')) {
                continue;
            }

            [$type, $path] = explode('|', $packed, 2);
            $items[(int) $position] = ['type' => $type, 'path' => $path];
        }

        foreach ($request->file('media_files', []) as $position => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $type = $request->input('media_types.'.$position) === 'video' ? 'video' : 'image';
            $items[(int) $position] = ['type' => $type, 'path' => $this->storeMediaFile($file, $type)];
        }

        ksort($items);

        return collect($items)->values()->map(fn (array $item, int $order) => [
            'type' => $item['type'],
            'path' => $item['path'],
            'order' => $order,
        ])->all();
    }

    /**
     * Saves an uploaded media file with the same safe-filename pattern as
     * before (slug + timestamp + random suffix) — the client-supplied
     * original filename is never trusted directly, which rules out path
     * traversal / double-extension tricks. ProductRequest's mimes + type
     * matching rules already reject bad uploads before this ever runs.
     * Images go to public/images/products, videos to public/videos/products.
     */
    private function storeMediaFile(UploadedFile $file, string $type): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'-'.time().'-'.Str::random(6).'.'.$extension;

        $directory = $type === 'video' ? 'videos/products' : 'images/products';

        if (! is_dir(public_path($directory))) {
            mkdir(public_path($directory), 0775, true);
        }

        $file->move(public_path($directory), $filename);

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
