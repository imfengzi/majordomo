<?php

namespace Chaos\Majordomo\Http\Controllers;

use Chaos\Majordomo\Exceptions\PermissionException;
use Chaos\Majordomo\Exceptions\RoleException;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);
        try {
            Role::create($request->all());
        } catch (RoleAlreadyExists $e) {
            throw new RoleException('该角色已存在');
        }
        return $this->response('添加成功');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'name' => 'required',
        ]);
        $role = Role::findOrFail(+$request->get('id'));
        try {
            $role->fill($request->all())->save();
        } catch (RoleAlreadyExists $e) {
            throw new RoleException('该角色已存在');
        }
        return $this->response('编辑成功');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);
        $role = Role::findOrFail($request->get('id'));
        $role->delete();
        return $this->response('删除成功');
    }

    public function get(Request $request)
    {
        $pageSize = $request->get('pageSize') ?? 20;
        $roles = Role::paginate($pageSize);
        return $this->pageReponse($roles);
    }

}
