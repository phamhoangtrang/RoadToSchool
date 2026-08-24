<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_capability_is_resolved_by_permission_content_instead_of_database_id(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();
        $updatePermission = $this->grant($admin, 'Update user permission');
        $this->grant($admin, 'Show user permission');

        $this->assertNotSame(19, $updatePermission->id);

        $response = $this->actingAs($admin)
            ->post("/admin/permissions/getPermission/{$target->id}")
            ->assertOk();

        $this->assertSame(1, $response->json('allowUpdate'));
    }

    public function test_permission_updates_are_validated_before_existing_assignments_are_replaced(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();
        $updatePermission = $this->grant($admin, 'Update user permission');
        $existingPermission = Permission::create([
            'content' => 'Existing permission',
            'group_permission' => Permission::STUDENT_GROUP_PERMISSION,
        ]);
        PermissionUser::create([
            'permission_id' => $existingPermission->id,
            'user_id' => $target->id,
        ]);

        $this->actingAs($admin)
            ->post("/admin/permissions/updatePermission/{$target->id}", [
                'checkedPermissionList' => [$updatePermission->id, $updatePermission->id],
            ])
            ->assertSessionHasErrors('checkedPermissionList.1');

        $this->assertDatabaseHas('permission_user', [
            'permission_id' => $existingPermission->id,
            'user_id' => $target->id,
        ]);

        $this->actingAs($admin)
            ->post("/admin/permissions/updatePermission/{$target->id}", [
                'checkedPermissionList' => [$updatePermission->id],
            ])
            ->assertOk();

        $this->assertDatabaseMissing('permission_user', [
            'permission_id' => $existingPermission->id,
            'user_id' => $target->id,
        ]);
        $this->assertDatabaseHas('permission_user', [
            'permission_id' => $updatePermission->id,
            'user_id' => $target->id,
        ]);
    }

    private function grant(User $user, string $content): Permission
    {
        $permission = Permission::create([
            'content' => $content,
            'group_permission' => Permission::ADMIN_GROUP_PERMISSION,
        ]);
        PermissionUser::create([
            'permission_id' => $permission->id,
            'user_id' => $user->id,
        ]);

        return $permission;
    }
}
