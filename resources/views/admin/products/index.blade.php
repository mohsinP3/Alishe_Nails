@extends('layouts.admin')

@section('title', 'Manage Products — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Products</h1>
            <p>{{ $products->total() }} products in your catalog</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Product
        </a>
    </div>

    <div class="admin-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Best Seller</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" style="width:44px;height:44px;object-fit:cover;border-radius:6px;" alt="{{ $product->name }}">
                            @else
                                <div style="width:44px;height:44px;border-radius:6px;background:var(--blush);"></div>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name ?? '—' }}</td>
                        <td>PKR {{ number_format($product->price, 0) }}</td>
                        <td>
                            {{ $product->stock }}
                            @if ($product->stock <= 5)
                                <span class="status-pill status-cancelled" style="margin-left:6px;">Low</span>
                            @endif
                        </td>
                        <td>{{ $product->is_best_seller ? 'Yes' : 'No' }}</td>
                        <td style="display:flex;gap:8px;">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline btn-sm">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ $product->name }}? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:#B3261E;color:#fff;border:none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;padding:30px;">No products yet — add your first one.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
            {{ $products->links() }}
        </div>
    </div>
@endsection
