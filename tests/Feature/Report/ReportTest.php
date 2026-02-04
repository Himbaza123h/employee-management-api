<?php

namespace Tests\Feature\Report;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function test_can_generate_excel_report(): void
    {
        ['token' => $token] = $this->authenticatedUser();

        $employee = Employee::factory()->create();
        Attendance::factory()->count(3)->create([
            'employee_id' => $employee->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/reports/attendance/excel');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_generate_excel_report_with_date_filter(): void
    {
        ['token' => $token] = $this->authenticatedUser();

        $employee = Employee::factory()->create();
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'attendance_date' => '2024-01-15',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/reports/attendance/excel?from_date=2024-01-01&to_date=2024-01-31');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_generate_excel_report_with_employee_filter(): void
    {
        ['token' => $token] = $this->authenticatedUser();

        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();

        Attendance::factory()->create(['employee_id' => $employee1->id]);
        Attendance::factory()->create(['employee_id' => $employee2->id]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/reports/attendance/excel?employee_id={$employee1->id}");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_generate_excel_report_with_all_filters(): void
    {
        ['token' => $token] = $this->authenticatedUser();

        $employee = Employee::factory()->create();
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'attendance_date' => '2024-01-15',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/reports/attendance/excel?from_date=2024-01-01&to_date=2024-01-31&employee_id=' . $employee->id);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_requires_authentication_for_excel_report(): void
    {
        $response = $this->getJson('/api/reports/attendance/excel');
        $response->assertStatus(401);
    }

    public function test_requires_authentication_for_pdf_report(): void
    {
        $response = $this->getJson('/api/reports/attendance/pdf');
        $response->assertStatus(401);
    }


}
