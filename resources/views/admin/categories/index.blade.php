@extends('layouts.admin')
@section('title', 'Categories — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Categories</h1>
            <p>{{ $categories->total() }} categories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Category
        </a>
    </div>

    <div class="admin-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Products</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td style="display:flex;gap:8px;">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-sm">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ $category->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:#B3261E;color:#fff;border:none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;padding:30px;">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
            {{ $categories->links() }}
        </div>
    </div>
@endsection
