<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_index_excludes_admin_accounts(): void
    {
        $admin = $this->createUser(['is_admin' => 1, 'role' => User::ROLE_ADMIN]);
        $student = $this->createUser();
        $this->grant($admin, 'View all users');

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertViewHas('users', fn ($users): bool => $users->pluck('id')->all() === [$student->id]);
    }

    public function test_admin_updates_the_requested_user_and_ignores_protected_fields(): void
    {
        $admin = $this->createUser(['is_admin' => 1, 'role' => User::ROLE_ADMIN]);
        $student = $this->createUser();
        $this->grant($admin, 'Update an user');

        $this->actingAs($admin)
            ->postJson("/admin/users/{$student->id}/updateUser", [
                'name' => 'Updated Student',
                'phone' => '0901234567',
                'personal_info' => 'Updated profile',
                'role' => User::ROLE_TEACHER,
                'is_admin' => 1,
                'email' => 'changed@example.com',
            ])
            ->assertOk()
            ->assertJsonPath('id', $student->id)
            ->assertJsonPath('name', 'Updated Student');

        $student->refresh();

        $this->assertSame('Updated Student', $student->name);
        $this->assertSame('0901234567', $student->phone);
        $this->assertSame(User::ROLE_TEACHER, $student->role);
        $this->assertFalse($student->is_admin);
        $this->assertNotSame('changed@example.com', $student->email);
    }

    public function test_admin_can_delete_another_user_but_not_their_own_account(): void
    {
        $admin = $this->createUser(['is_admin' => 1, 'role' => User::ROLE_ADMIN]);
        $student = $this->createUser();
        $this->grant($admin, 'View all users');

        $this->actingAs($admin)
            ->delete("/admin/users/{$student->id}")
            ->assertRedirect('/admin/users');

        $this->assertDatabaseMissing('users', ['id' => $student->id]);

        $this->actingAs($admin)
            ->delete("/admin/users/{$admin->id}")
            ->assertStatus(422);

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /** @param array<string, mixed> $attributes */
    private function createUser(array $attributes = []): User
    {
        return User::forceCreate(array_merge([
            'name' => 'Test Student',
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'avatar' => 'images/avatar/basic-avatar.png',
            'personal_info' => '',
            'role' => User::ROLE_STUDENT,
            'is_admin' => 0,
        ], $attributes));
    }

    private function grant(User $user, string $content): void
    {
        $permission = Permission::create([
            'content' => $content,
            'group_permission' => Permission::ADMIN_GROUP_PERMISSION,
        ]);

        PermissionUser::create([
            'permission_id' => $permission->id,
            'user_id' => $user->id,
        ]);
    }
}
