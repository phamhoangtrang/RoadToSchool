<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use App\Support\RoutePermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Tests\TestCase;

class RoutePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_protected_route_has_an_explicit_permission_mapping(): void
    {
        $protectedRoutes = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route): bool => in_array('access_page', $route->middleware(), true));

        $this->assertNotEmpty($protectedRoutes);

        foreach ($protectedRoutes as $route) {
            $this->assertNotNull(
                RoutePermissions::forRoute($route->getName()),
                "Missing permission mapping for route [{$route->getName()}].",
            );
        }
    }

    public function test_guest_is_redirected_to_login_before_permissions_are_checked(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_user_with_the_route_permission_can_access_admin_dashboard(): void
    {
        $admin = $this->createAdmin();
        $permission = Permission::create([
            'content' => 'Access admin dashboard',
            'group_permission' => Permission::ADMIN_GROUP_PERMISSION,
        ]);

        PermissionUser::create([
            'permission_id' => $permission->id,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_user_without_the_route_permission_receives_forbidden(): void
    {
        $this->actingAs($this->createAdmin())->get('/admin')->assertForbidden();
    }

    private function createAdmin(): User
    {
        return User::forceCreate([
            'name' => 'Test Admin',
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'avatar' => 'images/avatar/basic-avatar.png',
            'personal_info' => '',
            'role' => User::ROLE_ADMIN,
            'is_admin' => 1,
        ]);
    }
}
