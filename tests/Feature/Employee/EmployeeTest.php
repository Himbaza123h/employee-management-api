<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    private function authenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function test_can_create_employee(): void
    {
        ['token' => $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/employees', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'employee_identifier' => 'EMP001',
                'phone_number' => '+1234567890',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'first_name', 'last_name', 'email'],
            ]);

        $this->assertDatabaseHas('employees', [
            'email' => 'john.doe@example.com',
            'employee_identifier' => 'EMP001',
        ]);
    }

    public function test_can_list_employees(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        Employee::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/employees');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'first_name', 'last_name', 'email'],
                ],
                'meta',
            ]);
    }

    public function test_can_update_employee(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/employees/{$employee->id}", [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);
    }

    public function test_can_delete_employee(): void
    {
        ['token' => $token] = $this->authenticatedUser();
        $employee = Employee::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/employees/{$employee->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }
}
