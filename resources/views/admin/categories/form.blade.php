@extends('layouts.admin')

@section('title', ($category->exists ? 'Edit' : 'Add').' Category — Alishe Nails Admin')

@section('content')
    <div class="admin-page-head">
        <div>
            <h1>{{ $category->exists ? 'Edit' : 'Add' }} Category</h1>
        </div>
    </div>

    <div class="admin-card" style="max-width:480px;">
        <form action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
            @csrf
            @if ($category->exists) @method('PUT') @endif

            <div class="form-field" style="margin-bottom:18px;">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary">{{ $category->exists ? 'Update' : 'Add' }}</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection
