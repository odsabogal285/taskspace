<?php

namespace App\Http\Requests\Api\task;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class updateTaskList extends FormRequest
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
            'task_list' => 'exists:task_lists'
        ];
    }

    public function messages()
    {
        return [
            'task_list.exists' => 'Lista de tareas no encontrada'
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
