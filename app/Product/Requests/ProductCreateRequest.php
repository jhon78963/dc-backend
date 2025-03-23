<?php

namespace App\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ProductCreateRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('products', 'name')->where(function ($query) {
                    return $query->where('status', 'ACTIVE');
                })
            ],
            'category'          => 'required|exists:categories,id',
            'brand'             => 'required|exists:brands,id',
            'measurement_unit'  => 'required|exists:measurement_units,id',
            'sale_price '       => 'required|numeric', 
            'purchase_price '   => 'required|numeric', 
            'minimum_stock '    => 'required|numeric', 
            
            'barcode'           => 'nullable|string|max:100',
            //'internal_code'   => 'nullable|string|max:100',
        ];
    }

    protected function failedValidation(Validator $validator)
{
    throw new ValidationException($validator, response()->json([
        'errors' => $validator->errors()
    ], 422));
}

}
