<?php

namespace App\Http\Middleware;

use App\Models\PermissionUser;
use App\Support\RoutePermissions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissionToAccessPage
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $permission = RoutePermissions::forRoute($routeName);

        abort_unless($request->user() && $permission, 403, 'Sorry! No permissions here.');

        $hasPermission = PermissionUser::query()
            ->join('permissions', 'permissions.id', '=', 'permission_user.permission_id')
            ->where('permission_user.user_id', $request->user()->id)
            ->where('permissions.content', $permission)
            ->exists();

        abort_unless($hasPermission, 403, 'Sorry! No permissions here.');

        return $next($request);
    }
}
