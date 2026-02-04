<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'required', 'exists:employees,id'],
            'check_in' => ['sometimes', 'required', 'date'],
            'check_out' => ['nullable', 'date', 'after:check_in'],
            'attendance_date' => ['sometimes', 'required', 'date'],
        ];
    }
}
