<?php

namespace Chaos\Majordomo\Models;

use Chaos\Majordomo\Exceptions\PermissionException;
use \Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public static function ensureAllExist($permissions)
    {
        $count = Permission::whereIn('id', $permissions)->count();
        if ($count === count($permissions)) {
            return true;
        }
        throw new PermissionException('权限项不存在');
    }
}
