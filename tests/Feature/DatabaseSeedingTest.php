<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_creates_a_working_local_demo(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('courses', 3);
        $this->assertDatabaseCount('lectures', 6);
        $this->assertTrue(User::where('email', 'admin@roadtoschool.local')->exists());
        $this->assertTrue(User::where('email', 'instructor@roadtoschool.local')->exists());
        $this->assertTrue(User::where('email', 'student@roadtoschool.local')->exists());
        $this->assertSame(3, Course::count());
        $this->assertSame(6, Lecture::count());
    }

    public function test_modern_user_factory_supports_each_application_role(): void
    {
        $student = User::factory()->create();
        $instructor = User::factory()->instructor()->create();
        $admin = User::factory()->admin()->create();

        $this->assertSame(User::ROLE_STUDENT, $student->role);
        $this->assertSame(User::ROLE_TEACHER, $instructor->role);
        $this->assertSame(User::ROLE_ADMIN, $admin->role);
        $this->assertTrue($admin->is_admin);
    }
}
