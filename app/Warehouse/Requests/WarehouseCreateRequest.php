<?php

namespace App\Warehouse\Requests;

use App\Warehouse\Models\Warehouse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class WarehouseCreateRequest extends FormRequest
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
                Rule::unique('warehouses', 'name')->where(function ($query) {
                    return $query->where('is_deleted', false);
                })
            ],
            'location' => [
                'required',
                'string',
                'max:120'
            ],
            'type' => [
                'required',
                Rule::in(['PRINCIPAL', 'SECUNDARIO'])
            ]
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del almacén es obligatorio.',
            'name.string'   => 'El nombre del almacén debe ser una cadena de texto.',
            'name.max'      => 'El nombre del almacén no debe exceder los 120 caracteres.',
            'name.unique'   => 'El nombre del almacén ya está en uso.',

            'location.required' => 'La ubicación es obligatoria.',
            'location.string'   => 'La ubicación debe ser una cadena de texto.',
            'location.max'      => 'La ubicación no debe exceder los 120 caracteres.',

            'type.required' => 'El tipo de almacén es obligatorio.',
            'type.in'       => 'El tipo de almacén debe ser PRINCIPAL o SECUNDARIO.',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name'      => mb_strtoupper($this->name, 'UTF-8'),
            'location'  => mb_strtoupper($this->location, 'UTF-8'),
            'type'      => mb_strtoupper($this->type, 'UTF-8'),
        ]);
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('type') === 'PRINCIPAL') {
                $existingPrincipal = Warehouse::where('type', 'PRINCIPAL')
                    ->where('is_deleted', false)
                    ->exists();

                if ($existingPrincipal) {
                    $validator->errors()->add('type', 'Solo puede existir una almacén de tipo PRINCIPAL.');
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
