<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Permission;
use App\Models\PermissionUser;
use Illuminate\Support\Facades\Route;

class CheckPermissionToAccessPage
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin has all access
        if ($user->is_admin == 1) {
            return $next($request);
        }

        $permissionDefine = [
            'admin.dashboard' => 'Access admin dashboard',
            'admin.bills.index' => 'Show all bill',
            'admin.bills.show' => 'Show a bill',
            'admin.bills.updateStatus' => 'Update a bill status',
            'admin.permissions.updatePermission' => 'Update user permission',
            'admin.permissions.index' => 'Show user permission',
            'admin.permissions.getPermission' => 'Show user permission',
            'admin.permissions.create' => 'Add new permission',
            'admin.permissions.store' => 'Add new permission',
            'admin.courses.index' => 'View all course information',
            'admin.categories.index' => 'View all categories',
            'admin.lectures.requests.index' => 'View all lecture requests',
            'admin.lectures.requests.accept' => 'Accept a lecture request',
            'admin.users.index' => 'View all users',
            'admin.instructor_ranking' => 'View instructor ranking',
            'admin.users.create_instructor' => 'Create new instructor',
            'admin.conversations.waiting' => 'Check and reply all conversation waiting',
            'admin.users.update' => 'Update an user',
            'instructor.courses.index' => 'Show instructor\'s course',
            'instructor.dashboard' => 'Access instructor dashboard',
            'instructor.courses.show' => 'Show a instructor\'s course',
            'instructor.courses.lectures.create' => 'Create a lecture in course',
            'instructor.courses.lectures.store' => 'Create a lecture in course',
            'instructor.courses.create' => 'Create a course',
            'instructor.courses.store' => 'Create a course',
        ];

        $routeName = Route::currentRouteName();

        if (isset($permissionDefine[$routeName])) {
            $permissionContent = $permissionDefine[$routeName];
            $permission = Permission::where('content', $permissionContent)->first();

            if ($permission) {
                $hasPermission = PermissionUser::where('user_id', $user->id)
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (!$hasPermission) {
                    return abort(403, "Sorry ! No permissions here");
                }
            }
        }

        return $next($request);
    }
}
