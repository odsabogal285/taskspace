<?php

namespace App\Http\Requests\Api\task;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class storeTaskList extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required'
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre de la lista de tareas es obligatorio.'
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'response' => 'error',
            'data' => null,
            'error' => $validator->errors()],
            406));
    }
}
