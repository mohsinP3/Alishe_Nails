@extends('layouts.admin')
@section('title', ($product->exists ? 'Edit' : 'Add').' Product — Alishe Nails Admin')

@section('content')
    <div class="admin-page-head">
        <div>
            <h1>{{ $product->exists ? 'Edit' : 'Add' }} Product</h1>
            <p>{{ $product->exists ? 'Update details for '.$product->name : 'Add a new product to your catalog' }}</p>
        </div>
    </div>

    <div class="admin-card">
        <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if ($product->exists) @method('PUT') @endif

            <div class="form-grid">
                <div class="form-field full">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                    @error('name') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">— None —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label for="badge">Badge (optional)</label>
                    <input type="text" id="badge" name="badge" value="{{ old('badge', $product->badge) }}" placeholder="NEW / BEST SELLER / LIMITED">
                </div>

                <div class="form-field">
                    <label for="price">Price (PKR)</label>
                    <input type="number" step="0.01" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                    @error('price') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field">
                    <label for="compare_at_price">Compare-at Price (optional)</label>
                    <input type="number" step="0.01" id="compare_at_price" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}">
                </div>

                <div class="form-field">
                    <label for="shape">Shape</label>
                    <select id="shape" name="shape">
                        @foreach (['Almond', 'Coffin', 'Square', 'Stiletto'] as $shape)
                            <option value="{{ $shape }}" {{ old('shape', $product->shape) == $shape ? 'selected' : '' }}>{{ $shape }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label for="length">Length</label>
                    <select id="length" name="length">
                        @foreach (['Short', 'Medium', 'Long', 'Extra Long'] as $length)
                            <option value="{{ $length }}" {{ old('length', $product->length) == $length ? 'selected' : '' }}>{{ $length }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label for="finish">Finish</label>
                    <select id="finish" name="finish">
                        @foreach (['Glossy', 'Matte', 'Glitter/Chrome'] as $finish)
                            <option value="{{ $finish }}" {{ old('finish', $product->finish) == $finish ? 'selected' : '' }}>{{ $finish }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field">
                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock ?? 20) }}" required>
                </div>

                <div class="form-field full">
                    <label for="short_description">Short Description</label>
                    <input type="text" id="short_description" name="short_description" value="{{ old('short_description', $product->short_description) }}">
                </div>

                <div class="form-field full">
                    <label for="description">Full Description</label>
                    <textarea id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-field full" data-media-manager data-max-items="10" data-asset-base="{{ rtrim(asset('/'), '/') }}">
                    <label>Product Media</label>

                    <div class="media-manager__actions">
                        <button type="button" class="btn btn-outline btn-sm" data-media-add="image">
                            <i class="fa-solid fa-image"></i> Add Image
                        </button>
                        <button type="button" class="btn btn-outline btn-sm" data-media-add="video">
                            <i class="fa-solid fa-film"></i> Add Video
                        </button>
                    </div>

                    {{-- Hidden pickers opened by the buttons above --}}
                    <input type="file" data-media-picker="image" accept=".jpg,.jpeg,.png,.webp" hidden aria-hidden="true" tabindex="-1">
                    <input type="file" data-media-picker="video" accept=".mp4,.webm" hidden aria-hidden="true" tabindex="-1">

                    <small style="display:block;margin-top:6px;color:rgba(43,29,29,.65);">
                        Images: JPG, PNG or WebP up to 4MB. Videos: MP4 or WebM up to 20MB.
                        Mix 1–10 items in any order — drag cards or use the arrows to re-order, &#10005; to remove.
                    </small>
                    @error('media') <div class="error">{{ $message }}</div> @enderror
                    @error('media_files.*') <div class="error">{{ $message }}</div> @enderror
                    @error('existing_media.*') <div class="error">{{ $message }}</div> @enderror

                    {{-- Ordered thumbnail cards are rendered here by admin.js --}}
                    <div class="media-manager__list" data-media-list></div>

                    {{-- Current gallery bootstrapped into the manager on load --}}
                    <script type="application/json" data-media-existing>@json($product->media_items)</script>
                </div>

            </div>

            <div class="admin-product-options">
                <div class="form-field">
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }}>
                        Mark as Best Seller
                    </label>
                </div>

                <div class="form-field">
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                        Show in Featured Collections
                    </label>
                </div>

                <div class="form-field">
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                        Active (visible in shop)
                    </label>
                </div>
            </div>

            <div style="display:flex;gap:12px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">{{ $product->exists ? 'Update' : 'Add' }} Product</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection
