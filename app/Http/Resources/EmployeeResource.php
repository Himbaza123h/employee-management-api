<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'employee_identifier' => $this->employee_identifier,
            'phone_number' => $this->phone_number,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'attendances' => AttendanceResource::collection($this->whenLoaded('attendances')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
