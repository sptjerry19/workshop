<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'customer' => 'required|array',
            'customer.name' => 'required|string|max:255',
            'customer.phone'    => [
                'required',
                'string',
                'max:20',
                // <- notice the opening and closing `/` here:
                'regex:/^(0|\+84)(3|5|7|8|9)\d{8}$/',
            ],
            'customer.address' => 'required|string',
            'total' => 'required|numeric|min:0',
            'user_id' => 'nullable',
            'payment_method' => 'required|string|in:cod,momo,bank'
        ];
    }
}
