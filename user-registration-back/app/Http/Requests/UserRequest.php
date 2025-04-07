<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => preg_replace('/\D/', '', $this->cpf),
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'cpf' => ['required', 'size:11', 'regex:/^\d{11}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'User not found.',
            'name.required' => 'Name is required.',
            'birth_date.required' => 'Birth date is required.',
            'birth_date.before' => 'Birth date must be in the past.',
            'cpf.required' => 'CPF is required.',
            'cpf.size' => 'CPF must be exactly 11 digits.',
            'cpf.regex' => 'CPF must contain only numbers.',
        ];
    }
}
