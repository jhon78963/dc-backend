<?php

namespace App\Brand\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class BrandCreateRequest extends FormRequest
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
                Rule::unique('brands', 'name')->where(function ($query) {
                    return $query->where('is_deleted', false);
                })
            ]
        ];
    }

    protected function failedValidation(Validator $validator)
{
    throw new ValidationException($validator, response()->json([
        'errors' => $validator->errors()
    ], 422));
}

}
