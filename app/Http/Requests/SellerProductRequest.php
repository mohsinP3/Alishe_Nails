<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'shape' => ['nullable', 'string', 'in:Almond,Coffin,Square,Stiletto'],
            'length' => ['nullable', 'string', 'in:Short,Medium,Long,Extra Long'],
            'finish' => ['nullable', 'string', 'in:Glossy,Matte,Glitter/Chrome'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery' => ['nullable', 'array', 'max:8'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
