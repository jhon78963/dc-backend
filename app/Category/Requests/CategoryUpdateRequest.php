<?php

namespace App\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class CategoryUpdateRequest extends FormRequest
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
                Rule::unique('categories', 'name')->where(function ($query) {
                    return $query->where('is_deleted', false);
                })->ignore($this->route('category'))
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
        'name.required' => 'El nombre de la categoría es obligatorio.',
        'name.string'   => 'El nombre de la categoría debe ser una cadena de texto.',
        'name.max'      => 'El nombre de la categoría no debe exceder los 120 caracteres.',
        'name.unique'   => 'El nombre de la categoría ya está en uso.',

        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'name' => mb_strtoupper($this->name, 'UTF-8'),
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
