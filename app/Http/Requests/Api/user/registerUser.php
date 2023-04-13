<?php

namespace App\Http\Requests\Api\user;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class registerUser extends FormRequest
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
            'first_name' => [
                'required',
            ],
            'first_surname' => [
                'required'
            ],
            'email' => [
                'required',
                'unique:users'
            ],
            'password' => [
                'required',
                'min:8'
            ]
        ];
    }

    public function  messages()
    {
        return [
          'email.unique' => 'El correo electrónico ya está registrado.',
            'password.min' => 'El campo de contraseña debe tener al menos 8 caracteres.'
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors(),
            'data' => 'null'],
            406));
    }
}
