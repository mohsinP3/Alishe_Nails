<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerApplicationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:sellers,email'],
            'instagram_handle' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'product_details' => ['required', 'string', 'max:3000'],
            'password' => ['required', 'confirmed', 'string', 'min:8'],
        ];
    }
}
