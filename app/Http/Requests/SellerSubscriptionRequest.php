<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerSubscriptionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'payment_method' => ['required', 'in:bank_transfer,jazzcash_easypaisa'],
            'transaction_reference' => ['required', 'string', 'max:150'],
            'payment_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
