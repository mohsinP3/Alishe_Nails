<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route already behind the 'admin' guard middleware
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'shape' => ['nullable', 'string', 'in:Almond,Coffin,Square,Stiletto'],
            'length' => ['nullable', 'string', 'in:Short,Medium,Long,Extra Long'],
            'finish' => ['nullable', 'string', 'in:Glossy,Matte,Glitter/Chrome'],
            'badge' => ['nullable', 'string', 'max:30'],
            'is_best_seller' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
            // MIME + extension + size all checked here — Laravel's `image`
            // rule verifies actual image content, not just the filename.
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
