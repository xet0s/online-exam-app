<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhpMyAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_phpmyadmin()
    {
        $response = $this->get('/admin/phpmyadmin');

        $response->assertRedirect('/login');
    }

    public function test_instructor_cannot_access_phpmyadmin()
    {
        $instructor = User::create([
            'name' => 'Instructor User',
            'email' => 'instructor@test.com',
            'password' => bcrypt('password'),
            'role' => 'egitmen',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($instructor)->get('/admin/phpmyadmin');

        $response->assertStatus(403);
    }

    public function test_dean_cannot_access_phpmyadmin()
    {
        $dean = User::create([
            'name' => 'Dean User',
            'email' => 'dean@test.com',
            'password' => bcrypt('password'),
            'role' => 'dekan',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($dean)->get('/admin/phpmyadmin');

        $response->assertStatus(403);
    }

    public function test_admin_is_proxied_properly()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get('/admin/phpmyadmin');
        $this->assertNotEquals(403, $response->getStatusCode());
        $this->assertNotEquals(302, $response->getStatusCode());
        $this->assertTrue(in_array($response->getStatusCode(), [200, 502]));
    }
}
