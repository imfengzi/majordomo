<?php

namespace Chaos\Majordomo\Http\Controllers;

use Chaos\Majordomo\Exceptions\MenuException;
use Chaos\Majordomo\Http\Requests\Menu\CreateOrUpdateRequest;
use Chaos\Majordomo\Models\Menu;
use Illuminate\Http\Request;
use LogicException;

class MenuController extends Controller
{
    public function create(CreateOrUpdateRequest $request)
    {
        Menu::create(array_merge($request->all(), ['guard_name' => config('majordomo.guard_name')]));
        return $this->response('添加成功');
    }

    public function update(Request $request)
    {
        $menu = Menu::findOrFail(+$request->get('id'));
        try {
            $menu->fill($request->all())->save();
        } catch (LogicException $e) {
            if ($e->getMessage() == 'Node must not be a descendant.') {
                throw new MenuException('不可以选子级菜单为父级');
            }
        }
        return $this->response('编辑成功');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);
        $menu = Menu::findOrFail($request->get('id'));
        $menu->delete();
        return $this->response('删除成功');
    }

    public function getAll()
    {
        $menus = Menu::orderBy('sequence', 'desc')->get()->toTree();
        return $this->response($menus);
    }

    public function getOwn(Request $request)
    {
        $admin = $request->user();
        $permissions = $admin->getAllPermissions()->pluck('id')->toArray();
        $menus = Menu::get()->reject(function ($menu) use ($permissions, $admin) {
            return $menu->permission_id && array_search($menu->permission_id, $permissions) === false && !$admin->isSuper();
        })->toTree();
        return $this->response($menus);
    }

}
