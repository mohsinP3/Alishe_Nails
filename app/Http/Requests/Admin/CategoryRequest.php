<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route already behind the 'admin' guard middleware
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
        ];
    }
}
