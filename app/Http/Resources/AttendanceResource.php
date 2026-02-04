<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'attendance_date' => $this->attendance_date,
            'hours_worked' => $this->check_out
                ? round($this->check_in->diffInHours($this->check_out), 2)
                : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
