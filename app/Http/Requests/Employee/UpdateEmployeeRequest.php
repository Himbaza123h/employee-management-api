<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')->id;

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255',
                Rule::unique('employees')->ignore($employeeId)],
            'employee_identifier' => ['sometimes', 'required', 'string', 'max:255',
                Rule::unique('employees')->ignore($employeeId)],
            'phone_number' => ['sometimes', 'required', 'string', 'max:20'],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
