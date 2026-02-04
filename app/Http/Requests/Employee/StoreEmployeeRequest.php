<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:employees'],
            'employee_identifier' => ['required', 'string', 'max:255', 'unique:employees'],
            'phone_number' => ['required', 'string', 'max:20'],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
