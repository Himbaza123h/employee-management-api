<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function test_can_create_attendance_record(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/attendances', [
                'employee_id' => $employee->id,
                'attendance_date' => '2024-01-15',
                'check_in' => '2024-01-15 09:00:00',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'employee_id', 'attendance_date', 'check_in'],
            ]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'attendance_date' => '2024-01-15',
        ]);
    }

    public function test_can_list_attendance_records(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();
        Attendance::factory()->count(5)->create(['employee_id' => $employee->id]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/attendances');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'employee_id', 'attendance_date', 'check_in'],
                ],
                'meta',
            ]);
    }

    public function test_can_filter_attendances_by_employee(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee1 = Employee::factory()->create();
        $employee2 = Employee::factory()->create();

        Attendance::factory()->count(3)->create(['employee_id' => $employee1->id]);
        Attendance::factory()->count(2)->create(['employee_id' => $employee2->id]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/attendances?employee_id={$employee1->id}");

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_filter_attendances_by_date_range(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();

        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'attendance_date' => '2024-01-15',
        ]);
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'attendance_date' => '2024-01-20',
        ]);
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'attendance_date' => '2024-02-15',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/attendances?from_date=2024-01-01&to_date=2024-01-31');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_show_single_attendance_record(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();
        $attendance = Attendance::factory()->create(['employee_id' => $employee->id]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/attendances/{$attendance->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $attendance->id,
                    'employee_id' => $employee->id,
                ],
            ]);
    }

    public function test_can_update_attendance_record(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();
        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'check_in' => '2024-01-15 09:00:00',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/attendances/{$attendance->id}", [
                'check_in' => '2024-01-15 09:30:00',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
        ]);
    }

    public function test_can_delete_attendance_record(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();
        $attendance = Attendance::factory()->create(['employee_id' => $employee->id]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/attendances/{$attendance->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
    }

    public function test_can_check_out_employee(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();
        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'check_in' => '2024-01-15 09:00:00',
            'check_out' => null,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/attendances/{$attendance->id}/check-out", [
                'check_out' => '2024-01-15 17:00:00',
            ]);

        $response->assertStatus(200);

        $attendance->refresh();
        $this->assertNotNull($attendance->check_out);
    }

    public function test_cannot_check_out_already_checked_out_attendance(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();
        $attendance = Attendance::factory()->create([
            'employee_id' => $employee->id,
            'check_in' => '2024-01-15 09:00:00',
            'check_out' => '2024-01-15 17:00:00',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/attendances/{$attendance->id}/check-out", [
                'check_out' => '2024-01-15 18:00:00',
            ]);

        // Your controller returns 400 for this case, not 422
        $response->assertStatus(400);
    }

    public function test_requires_authentication_for_attendance_operations(): void
    {
        $employee = Employee::factory()->create();
        $attendance = Attendance::factory()->create(['employee_id' => $employee->id]);

        // Test each endpoint without auth
        $this->getJson('/api/attendances')->assertStatus(401);
        $this->postJson('/api/attendances', [])->assertStatus(401);
        $this->getJson("/api/attendances/{$attendance->id}")->assertStatus(401);
        $this->putJson("/api/attendances/{$attendance->id}", [])->assertStatus(401);
        $this->deleteJson("/api/attendances/{$attendance->id}")->assertStatus(401);
        $this->postJson("/api/attendances/{$attendance->id}/check-out", [])->assertStatus(401);
    }

    public function test_validates_required_fields_when_creating_attendance(): void
    {
        ['token' => $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/attendances', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['employee_id']);
    }

    public function test_validates_employee_exists_when_creating_attendance(): void
    {
        ['token' => $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/attendances', [
                'employee_id' => 99999,
                'attendance_date' => '2024-01-15',
                'check_in' => '2024-01-15 09:00:00',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['employee_id']);
    }

    public function test_validates_date_format(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/attendances', [
                'employee_id' => $employee->id,
                'attendance_date' => 'invalid-date',
                'check_in' => '2024-01-15 09:00:00',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['attendance_date']);
    }
}
