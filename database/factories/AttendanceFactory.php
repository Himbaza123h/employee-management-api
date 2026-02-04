<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('-30 days', 'now');
        $checkOut = $this->faker->boolean(70)
            ? $this->faker->dateTimeBetween($checkIn, '+10 hours')
            : null;

        return [
            'employee_id' => Employee::factory(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'attendance_date' => $checkIn->format('Y-m-d'),
        ];
    }

    public function withoutCheckOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_out' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $checkIn = $attributes['check_in'];
            return [
                'check_out' => $this->faker->dateTimeBetween($checkIn, '+10 hours'),
            ];
        });
    }
}
