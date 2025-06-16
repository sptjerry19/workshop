<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'image' => ['sometimes', 'string'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'is_active' => ['sometimes', 'boolean'],
            'options' => ['sometimes', 'array'],
            'options.*' => ['exists:options,id'],
            'toppings' => ['sometimes', 'array'],
            'toppings.*' => ['exists:toppings,id'],
            'size' => 'nullable|array',
            'size.*.label' => 'required|string',
            'size.*.price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount' => 'nullable|numeric|min:0'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'product name',
            'image' => 'product image',
            'price' => 'product price',
            'size' => 'product size',
            'description' => 'product description',
            'category_id' => 'category ID',
            'stock' => 'product stock',
            'discount' => 'product discount'
        ];
    }
    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The :attribute field is required.',
            'image.required' => 'The :attribute field is required.',
            'price.required' => 'The :attribute field is required.',
            'size.array' => 'The :attribute must be an array.',
            'size.*.label.required' => 'The size label is required.',
            'size.*.price.required' => 'The size price is required.',
            'description.required' => 'The :attribute field is required.',
            'category_id.required' => 'The :attribute field is required.',
            'stock.required' => 'The :attribute field is required.',
            'discount.numeric' => 'The :attribute must be a number.'
        ];
    }
}
