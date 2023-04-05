<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class storeTask extends FormRequest
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
            'task_list_id' => [
                'required',
                'numeric'
            ],
            'name' => [
                'required'
            ],
            'description' => [
                'required'
            ],
            'finished' => [
                'required',
                'boolean'
            ]
        ];
    }
}
