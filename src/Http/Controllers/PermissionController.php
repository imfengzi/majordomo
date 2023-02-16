<?php

namespace Chaos\Majordomo\Http\Controllers;

use Chaos\Majordomo\Exceptions\PermissionException;
use Chaos\Majordomo\Http\Requests\Permission\CreateOrUpdateRequest;
use Chaos\Majordomo\Models\Permission;
use Chaos\Majordomo\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;

class PermissionController extends Controller
{
    public function create(CreateOrUpdateRequest $request)
    {
        try {
            Permission::create($request->all());
        } catch (PermissionAlreadyExists $e) {
            throw new PermissionException('该权限已存在');
        }
        return $this->response('添加成功');
    }

    public function update(CreateOrUpdateRequest $request)
    {
        $permission = Permission::findOrFail($request->get('id'));
        try {
            $permission->fill($request->all())->save();
        } catch (PermissionAlreadyExists $e) {
            throw new PermissionException('该权限已存在');
        }
        return $this->response('编辑成功');
    }

    public function getCategories()
    {
        $categories = Permission::distinct('category')->select('category as name')->get();
        return $this->response($categories);
    }

    public function updateCategory(Request $request)
    {
        $validated = $request->validate([
            'old' => 'required',
            'new' => 'required',
        ]);
        $affected = Permission::where(['category' => $validated['old']])->update(['category' => $validated['new']]);
        if ($affected) {
            return $this->response('更新成功');
        }
        throw  new PermissionException('分类不存在');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);

        $permission = Permission::withCount('menus')->findOrFail($request->get('id'));
        if ($permission->menus_count) {
            throw  new PermissionException('该权限有绑定菜单请先取消');
        };

        $permission->delete();
        return $this->response('删除成功');
    }

    public function getOwn(Request $request)
    {
        $admin = $request->user();
        if ($admin->username === 'admin') {
            return $this->response(Permission::all());
        }
        return $this->response($admin->getAllPermissions());
    }

    public function get(Request $request)
    {
        return $this->response(Permission::all());
    }


    public function grant(Request $request)
    {
        $admin = $request->user();
        $permissions = $request->get('permissions');
        if ($admin->isSuper() || $admin->hasAllDirectPermissions($permissions)) {
            $role = Role::findOrFail($request->get('role_id'));
            $role->syncPermissions($request->get('permissions'));
            return $this->response('授权成功');
        } else {
            throw new PermissionException('当前用户没有部分权限, 无法授权');
        }
    }

    public function categoryExist(Request $request)
    {
        $category = $request->get('category');
        $count = Permission::where('category', $category)->count();
        return $this->response(!!$count);
    }

    public function getByRole(Request $request)
    {
        $role = Role::findOrFail(+$request->get('id'));
        return $this->response($role->permissions);
    }
}
