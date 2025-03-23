<?php

namespace App\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ProductUpdateRequest extends FormRequest
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
                'max:120',
                Rule::unique('products', 'name')->where(function ($query) {
                    return $query->where('is_deleted', false);
                })->ignore($this->route('product'))
            ],
            'categoryId' => [
                'required',
                'exists:categories,id',  
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('is_deleted', false);  
                }),
            ],
            'brandId' => [
                'required',
                'exists:brands,id',  
                Rule::exists('brands', 'id')->where(function ($query) {
                    return $query->where('is_deleted', false);  
                }),
            ],
            'measurementUnitId' => [
                'required',
                'exists:measurement_units,id', 
                Rule::exists('measurement_units', 'id')->where(function ($query) {
                    return $query->where('is_deleted', false); 
                }),
            ],    
            'salePrice'         => 'required|numeric|gt:0',            
            'purchasePrice'     => 'required|numeric', 
            'minimumStock'      => 'required|numeric|gt:0', 
            
            'barcode'           => 'nullable|string|max:100',
            //'internalCode'    => 'nullable|string|max:100',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name' => mb_strtoupper($this->name, 'UTF-8'),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required'            => 'El nombre es obligatorio.',
            'name.string'              => 'El nombre debe ser una cadena de texto.',
            'name.max'                 => 'El nombre no debe tener más de 200 caracteres.',
            'name.unique'              => 'El nombre ya está registrado y no está disponible.',

            'categoryId.required'        => 'La categoría es obligatoria.',
            'categoryId.exists'          => 'La categoría seleccionada no es válida.',

            'brandId.required'           => 'La marca es obligatoria.',
            'brandId.exists'             => 'La marca seleccionada no es válida.',

            'measurementUnitId.required'  => 'La unidad de medida es obligatoria.',
            'measurementUnitId.exists'    => 'La unidad de medida seleccionada no es válida.',

            'salePrice.required'      => 'El precio de venta es obligatorio.',
            'salePrice.numeric'       => 'El precio de venta debe ser un valor numérico.',
            'salePrice.gt'            => 'El precio de venta debe ser mayor que 0.',  

            'purchasePrice.required'  => 'El precio de compra es obligatorio.',
            'purchasePrice.numeric'   => 'El precio de compra debe ser un valor numérico.',

            'minimumStock.required'   => 'El stock mínimo es obligatorio.',
            'minimumStock.numeric'    => 'El stock mínimo debe ser un valor numérico.',
            'minimumStock.gt'         => 'El stock mínimo debe ser mayor a 0.',

            'barcode.string'           => 'El código de barras debe ser una cadena de texto.',
            'barcode.max'              => 'El código de barras no debe tener más de 100 caracteres.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
