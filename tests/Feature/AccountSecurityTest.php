<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_only_update_their_own_profile_and_cannot_change_protected_fields(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();

        $this->actingAs($student)->put("/users/{$otherStudent->id}", [
            'update_info' => 'Update',
            'name' => 'Compromised Name',
            'address' => 'Hanoi',
            'grade' => User::GRADE_10,
        ])->assertForbidden();
        $this->assertNotSame('Compromised Name', $otherStudent->fresh()->name);

        $originalEmail = $student->email;
        $this->actingAs($student)->put("/users/{$student->id}", [
            'update_info' => 'Update',
            'name' => 'Updated Student',
            'address' => 'Ho Chi Minh City',
            'grade' => User::GRADE_10,
            'email' => 'attacker@example.com',
            'role' => User::ROLE_TEACHER,
            'is_admin' => true,
        ])->assertRedirect("/users/{$student->id}");

        $student->refresh();
        $this->assertSame('Updated Student', $student->name);
        $this->assertSame($originalEmail, $student->email);
        $this->assertSame(User::ROLE_STUDENT, $student->role);
        $this->assertFalse($student->is_admin);
    }

    public function test_password_change_requires_the_current_password_and_a_strong_confirmation(): void
    {
        $student = User::factory()->create(['password' => Hash::make('Current123')]);

        $this->actingAs($student)->put("/users/{$student->id}", [
            'update_password' => 'Update password',
            'old_password' => 'Wrong123',
            'new_password' => 'Updated123',
            'password_confirmation' => 'Updated123',
        ])->assertSessionHasErrors('old_password');
        $this->assertTrue(Hash::check('Current123', $student->fresh()->password));

        $this->actingAs($student)->put("/users/{$student->id}", [
            'update_password' => 'Update password',
            'old_password' => 'Current123',
            'new_password' => 'Updated123',
            'password_confirmation' => 'Updated123',
        ])->assertRedirect("/users/{$student->id}");

        $this->assertTrue(Hash::check('Updated123', $student->fresh()->password));
    }

    public function test_registration_requires_a_modern_minimum_password(): void
    {
        $this->post('/register', [
            'name' => 'New Student',
            'email' => 'new.student@example.com',
            'password' => 'short1',
            'password_confirmation' => 'short1',
        ])->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'new.student@example.com']);

        $this->post('/register', [
            'name' => 'New Student',
            'email' => 'new.student@example.com',
            'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123',
        ])->assertRedirect('/home');

        $user = User::where('email', 'new.student@example.com')->sole();
        $this->assertSame(User::ROLE_STUDENT, $user->role);
        $this->assertFalse($user->is_admin);
    }
}
