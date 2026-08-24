<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdminPermissionRequest;
use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use App\Support\RoutePermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    protected $modelPermission;

    protected $modelUser;

    protected $modelPermissionUser;

    public function __construct(Permission $permission, User $user, PermissionUser $permissionUser)
    {
        $this->modelPermission = $permission;
        $this->modelUser = $user;
        $this->modelPermissionUser = $permissionUser;
    }

    public function index(Request $request)
    {
        $userGroupList = Permission::$user_group;
        $permissionGroupList = Permission::$permission_group;
        // Group Permission
        for ($i = 0; $i < 3; $i++) {
            $commonPermission[$i] = $this->modelPermission->where('group_permission', $i)->get();
        }
        $adminList = $this->modelUser->where('is_admin', 1)->get();
        $instructorList = $this->modelUser->where('is_admin', 0)->where('role', 1)->get();
        $studentList = $this->modelUser->where('is_admin', 0)->where('role', 2)->get();
        $dataParamList = $request->all();

        $emailUserList = $this->modelUser->pluck('email', 'id');
        $editPermission = 0;

        return view('admin.permissions.index', compact(
            'userGroupList',
            'permissionGroupList',
            'commonPermission',
            'adminList',
            'instructorList',
            'studentList',
            'emailUserList',
            'editPermission',
            'dataParamList'
        ));
    }

    public function getPermission(Request $request, $userId)
    {
        $permissionList = $this->modelPermissionUser->where('user_id', $userId)->pluck('permission_id');
        $selectedUser = User::findOrFail($userId);

        $responseData['allowUpdate'] = $this->modelPermissionUser
            ->join('permissions', 'permissions.id', '=', 'permission_user.permission_id')
            ->where('permission_user.user_id', $request->user()->id)
            ->where('permissions.content', RoutePermissions::MAP['admin.permissions.updatePermission'])
            ->exists() ? 1 : 0;

        $responseData['permissionList'] = $permissionList;
        $responseData['selectedUser'] = $selectedUser;

        return json_encode($responseData);
    }

    public function updatePermission(Request $request, $userId)
    {
        $this->modelUser->findOrFail($userId);
        $data = $request->validate([
            'checkedPermissionList' => ['sometimes', 'array'],
            'checkedPermissionList.*' => ['integer', 'distinct', 'exists:permissions,id'],
        ]);
        $checkedPermissionList = $data['checkedPermissionList'] ?? [];

        DB::transaction(function () use ($userId, $checkedPermissionList): void {
            $this->modelPermissionUser->where('user_id', $userId)->delete();
            foreach ($checkedPermissionList as $permissionId) {
                $this->modelPermissionUser->create([
                    'permission_id' => $permissionId,
                    'user_id' => $userId,
                ]);
            }
        });

        return json_encode($checkedPermissionList);
    }

    public function create()
    {
        $permissionGroupList = Permission::$permission_group;
        // Group Permission
        for ($i = 0; $i < 3; $i++) {
            $commonPermission[$i] = $this->modelPermission->where('group_permission', $i)->get();
        }

        return view('admin.permissions.create', compact(
            'permissionGroupList',
            'commonPermission'
        ));
    }

    public function store(CreateAdminPermissionRequest $request)
    {
        $data = $request->all();

        $result = $this->modelPermission->create($data);
        if ($result) {
            flash(__('messages.create_permission_successfully'))->success();
        } else {
            flash(__('messages.create_permission_failed'))->error();
        }

        return redirect()->back();
    }
}
