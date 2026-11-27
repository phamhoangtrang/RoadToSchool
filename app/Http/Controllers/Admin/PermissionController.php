<?php

namespace App\Http\Controllers\Admin;

use App\Models\PermissionUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use App\Http\Requests\CreateAdminPermissionRequest;

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

        // Chỉ super admin hoặc người có quyền "Update user permission" mới được sửa
        $responseData['allowUpdate'] = $this->canUpdatePermission(\Auth::user()) && !$selectedUser->isSuperAdmin() ? 1 : 0;

        $responseData['permissionList'] = $permissionList;
        $responseData['selectedUser'] = $selectedUser;

        return json_encode($responseData);
    }

    private function canUpdatePermission($user)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->permissions()->where('content', 'Update user permission')->exists();
    }

    public function updatePermission(Request $request, $userId)
    {
        $actor = \Auth::user();
        if (!$this->canUpdatePermission($actor)) {
            abort(403, 'Bạn không có quyền cập nhật phân quyền.');
        }

        $targetUser = User::findOrFail($userId);
        if ($targetUser->isSuperAdmin()) {
            abort(403, 'Không thể thay đổi quyền của Super Admin.');
        }

        $request->validate([
            'checkedPermissionList' => 'nullable|array',
            'checkedPermissionList.*' => 'integer|exists:permissions,id',
        ]);

        $checkedPermissionList = array_values(array_unique(array_map('intval', $request->input('checkedPermissionList', []))));

        // Admin không thể tự tước quyền sửa phân quyền của chính mình
        if ($actor->id === $targetUser->id) {
            $keepId = $this->modelPermission->where('content', 'Update user permission')->value('id');
            if ($keepId && !in_array($keepId, $checkedPermissionList)) {
                $checkedPermissionList[] = $keepId;
            }
        }

        \DB::transaction(function () use ($targetUser, $checkedPermissionList) {
            $this->modelPermissionUser->where('user_id', $targetUser->id)->delete();
            foreach ($checkedPermissionList as $permissionId) {
                $this->modelPermissionUser->create([
                    'permission_id' => $permissionId,
                    'user_id' => $targetUser->id,
                ]);
            }
        });

        return empty($checkedPermissionList) ? 200 : json_encode($checkedPermissionList);
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
