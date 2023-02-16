<?php

namespace Chaos\Majordomo\Http\Controllers;

use Chaos\Majordomo\Exceptions\PermissionException;
use Chaos\Majordomo\Exceptions\RoleException;
use Chaos\Majordomo\Exceptions\RouteException;
use Chaos\Majordomo\Models\Permission;
use Chaos\Majordomo\Models\Route;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role;

class RouteController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'path' => 'required',
            'desc' => 'required',
        ]);
        $count = Route::where('path', $request->get('path'))->count();
        if ($count > 0) {
            throw new RouteException('该路由已经存在');
        }
        $route = Route::create($request->all());
        $permissions = $request->get('permissions');
        if ($permissions) {
            $permissions = explode(',', $permissions);
            Permission::ensureAllExist($permissions);
            $route->permissions()->sync($permissions);
        }
        return $this->response('添加成功');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',
            'path' => 'required',
            'desc' => 'required',
        ]);
        $route = Route::findOrFail($request->get('id'));
        $path = $request->get('path');
        if ($route->path != $path) {
            $count = Route::where('path', $path)->where('id', '<>', $route->id)->count();
            if ($count > 0) {
                throw new RouteException('该路由已经存在');
            }
        }
        $route->fill($request->all())->save();
        $permissions = $request->get('permissions');
        if ($permissions) {
            $permissions = explode(',', $permissions);

            Permission::ensureAllExist($permissions);
            $route->permissions()->sync($permissions);
        }
        return $this->response('编辑成功');
    }


    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);
        $route = Route::findOrFail($request->get('id'));
        $route->delete();
        return $this->response('删除成功');
    }

    public function get(Request $request)
    {
        $pageSize = $request->get('pageSize') ?? 20;
        $roles = Route::with('permissions')->paginate($pageSize);
        return $this->pageReponse($roles);
    }

}
