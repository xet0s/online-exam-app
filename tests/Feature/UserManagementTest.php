<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_management_list()
    {
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    public function test_instructor_cannot_view_user_management_list()
    {
        $instructor = User::create([
            'name' => 'Instructor User',
            'email' => 'instructor@test.com',
            'password' => bcrypt('password'),
            'role' => 'egitmen',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($instructor)->get(route('users.index'));

        $response->assertStatus(403);
    }

    public function test_dean_cannot_approve_deans_or_admins()
    {
        $dean = User::create([
            'name' => 'Dean User',
            'email' => 'dean@test.com',
            'password' => bcrypt('password'),
            'role' => 'dekan',
            'status' => 'approved',
        ]);

        $targetDean = User::create([
            'name' => 'Target Dean',
            'email' => 'target@test.com',
            'password' => bcrypt('password'),
            'role' => 'dekan',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($dean)->post(route('approvals.approve', $targetDean->id));

        $response->assertStatus(403);
        $this->assertEquals('pending', $targetDean->fresh()->status);
    }
}
