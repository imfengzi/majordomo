<?php

namespace Chaos\Majordomo\Http\Controllers;

use Chaos\Majordomo\Exceptions\AdminException;
use Chaos\Majordomo\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        $admin = Admin::where('username', $validated['username'])->first();
        $needRestrict = $this->needRestrict();
        if ($needRestrict && $admin->isLocked()) {
            throw new AdminException('您已被锁定，请稍后再试');
        }
        if (!$admin || !$admin->verifyPassword($validated['password'])) {
            if ($needRestrict) {
                $admin->lockOnce()->save();
            }
            throw new AdminException('账号或密码错误');
        }

        if ($needRestrict) {
            $admin->clearLock();
        }

        if (!$this->canMultipleLogin()) {
            $admin->tokens()->delete();
        }

        return $this->response($admin->createToken(config('majordomo.guard_name'))->plainTextToken);
    }

    public function modifyPassword(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required',
            'old_password' => 'required',
        ]);
        $admin = $request->user();
        if (!$admin->verifyPassword($request->get('old_password'))) {
            throw new AdminException('旧密码不正确');
        }
        $admin->password = $request->get('password');
        $admin->save();
        $admin->tokens()->delete();
        return $this->response('修改成功');
    }


    public function create(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required',
            'role_id' => 'required|numeric'
        ]);
        if (Admin::where(['username' => $validated['username']])->count()) {
            throw new AdminException('该用户名已存在');
        }
        $password = Admin::randPassword();
        $admin = Admin::create([
            'username' => $validated['username'],
            'password' => $password
        ]);
        $admin->assignRole($validated['role_id']);
        return $this->response(['password' => $password]);
    }

    public function resetPassword(Request $request)
    {
        $admin = Admin::findOrFail($request->get('id'));
        $password = Admin::randPassword();
        $admin->password = $password;
        $admin->clearLock(false);
        $admin->save();
        return $this->response(['password' => $password]);
    }


    public function needRestrict()
    {
        return config('majordomo.retry_num') > 0;
    }

    public function canMultipleLogin()
    {
        return config('majordomo.allow_multiple_login');
    }

    public function grant(Request $request)
    {
        $admin = Admin::findOrFail($request->get('admin_id'));
        $admin->assignRole($request->get('role_id'));
        return $this->response('保存成功');
    }

    public function get(Request $request)
    {
        $pageSize = $request->get('pageSize') ?? 20;
        $username = $request->get('username');
        if ($username) {
            $admins = Admin::where('username', 'like', "%${username}%")->paginate($pageSize);
        } else {
            $admins = Admin::paginate($pageSize);
        }
        return $this->pageReponse($admins);
    }

    public function delete(Request $request)
    {
        $admin = Admin::findOrFail($request->get('id'));
        $admin->delete();
        return $this->response('删除成功');
    }

    public function getCurrent(Request $request)
    {
        $admin = $request->user();
        $roles = $admin->isSuper() ? ['超级管理员'] : $admin->getRoleNames();
        return $this->response(array_merge($admin->toArray(), ['roles' => $roles]));
    }

    public function logout(Request $request)
    {
        $admin = $request->user();
        $admin->currentAccessToken()->delete();
        return $this->response('退出成功');
    }
}
