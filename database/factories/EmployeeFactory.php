<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'employee_identifier' => 'EMP' . $this->faker->unique()->numberBetween(1000, 9999),
            'phone_number' => $this->faker->phoneNumber(),
            'user_id' => null,
        ];
    }
}



