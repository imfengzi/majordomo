<?php

namespace Chaos\Majordomo\Database;

use Chaos\Majordomo\Models\Admin;
use Chaos\Majordomo\Models\Menu;
use Chaos\Majordomo\Models\Role;
use Chaos\Majordomo\Models\Route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class MajordomoTableSeeder extends Seeder
{

    private $guardName;

    public function __construct()
    {
        $this->guardName = config('majordomo.guard_name');
    }

    public function run()
    {
        Cache::forget('spatie.permission.cache');
        $this->createPermissions();
        $this->createRoles();
        $this->createMenus();
        $this->createAdmin();
        $this->createApis();
    }

    private function createPermissions()
    {
        $permissions = [
            ["name" => "menu:manage", "alias" => "菜单管理", "category" => "菜单"],
            ["name" => "menu:create", "alias" => "添加菜单", "category" => "菜单"],
            ["name" => "menu:update", "alias" => "编辑菜单", "category" => "菜单"],
            ["name" => "menu:delete", "alias" => "删除菜单", "category" => "菜单"],
            ["name" => "api:manage", "alias" => "接口权限管理", "category" => "接口"],
            ["name" => "api:create", "alias" => "添加接口", "category" => "接口"],
            ["name" => "api:update", "alias" => "编辑接口", "category" => "接口"],
            ["name" => "api:delete", "alias" => "删除接口", "category" => "接口"],
            ["name" => "permission:manage", "alias" => "权限管理", "category" => "权限"],
            ["name" => "permission:create", "alias" => "添加权限", "category" => "权限"],
            ["name" => "permission:update", "alias" => "编辑权限", "category" => "权限"],
            ["name" => "permission:delete", "alias" => "删除权限", "category" => "权限"],
            ["name" => "admin:manage", "alias" => "管理员管理", "category" => "管理员"],
            ["name" => "admin:reset", "alias" => "重置管理员密码", "category" => "管理员"],
            ["name" => "admin:create", "alias" => "添加管理员", "category" => "管理员"],
            ["name" => "admin:delete", "alias" => "删除管理员", "category" => "管理员"],
            ["name" => "role:manage", "alias" => "角色管理", "category" => "角色"],
            ["name" => "role:create", "alias" => "添加角色", "category" => "角色"],
            ["name" => "role:update", "alias" => "编辑角色", "category" => "角色"],
            ["name" => "role:delete", "alias" => "删除角色", "category" => "角色"],
            ["name" => "role:grant", "alias" => "角色授权", "category" => "角色"]
        ];
        Permission::query()->delete();
        foreach ($permissions as $permission) {
            Permission::create(array_merge($permission, ['guard_name' => $this->guardName]));
        }
    }

    private function createMenus()
    {
        $menus = [
            [
                "name"          => "管理员管理",
                "permission_id" => "admin:manage",
                "sequence"      => 1,
                "path"          => "/admin",
                "icon"          => "UserOutlined"
            ],
            [
                "name"          => "菜单管理",
                "permission_id" => "menu:manage",
                "sequence"      => 1,
                "path"          => "/menu",
                "icon"          => "UnorderedListOutlined"
            ],
            [
                "name"          => "角色管理",
                "permission_id" => "role:manage",
                "sequence"      => 1,
                "path"          => "/role",
                "icon"          => "TeamOutlined"
            ],
            [
                "name"          => "权限管理",
                "permission_id" => "permission:manage",
                "sequence"      => 1,
                "path"          => "/permission",
                "icon"          => "LockOutlined"
            ],
            [
                "name"          => "接口权限管理",
                "permission_id" => "api:manage",
                "sequence"      => 1,
                "path"          => "/route",
                "icon"          => "PaperClipOutlined"
            ],
        ];
        Menu::truncate();
        foreach ($menus as $menu) {
            $permission = Permission::findByName($menu['permission_id'], $this->guardName);
            Menu::create(array_merge($menu, ['permission_id' => $permission->id, 'guard_name' => $this->guardName]));
        }
    }

    private function createRoles()
    {
        $roles = [
            [
                'name' => '管理员',
            ]
        ];
        Role::query()->delete();
        foreach ($roles as $role) {
            $newRole = Role::create(array_merge($role, ['guard_name' => $this->guardName]));
            $newRole->givePermissionTo(Permission::get());
        }
    }

    private function createAdmin()
    {
        Admin::truncate();

        $admin = Admin::create([
            'username' => 'admin',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        ]);
        $admin->assignRole('管理员');
    }

    private function createApis()
    {
        $apis = [
            [
                "path"        => "/admin/password/reset",
                "desc"        => "重置管理员密码",
                "permissions" => ["admin:reset"]
            ],
            [
                "path"        => "/admin/create",
                "desc"        => "添加管理员",
                "permissions" => ["admin:create"]
            ],
            [
                "path"        => "/admin/delete",
                "desc"        => "删除管理员",
                "permissions" => ["admin:delete"]
            ],
            [
                "path"        => "/admin/get",
                "desc"        => "获取管理员列表",
                "permissions" => ["admin:manage"]
            ],
            [
                "path"        => "/menu/create",
                "desc"        => "添加菜单",
                "permissions" => ["menu:create"]
            ],
            [
                "path"        => "/menu/update",
                "desc"        => "编辑菜单",
                "permissions" => ["menu:update"]
            ],
            [
                "path"        => "/menu/delete",
                "desc"        => "删除菜单",
                "permissions" => ["menu:delete"]
            ],
            [
                "path"        => "/menu/all",
                "desc"        => "获取全部菜单",
                "permissions" => ["menu:manage"]
            ],
            [
                "path"        => "/permission/create",
                "desc"        => "添加权限",
                "permissions" => ["permission:create"]
            ],
            [
                "path"        => "/permission/update",
                "desc"        => "编辑权限",
                "permissions" => ["permission:update"]
            ],
            [
                "path"        => "permission/categories",
                "desc"        => "获取权限分组",
                "permissions" => ["permission:manage", "permission:create", "permission:update"]
            ],
            [
                "path"        => "/permission/category/update",
                "desc"        => "编辑权限分组",
                "permissions" => ["permission:update"]
            ],
            [
                "path"        => "/permission/delete",
                "desc"        => "删除权限",
                "permissions" => ["menu:delete"]
            ],
            [
                "path"        => "/permission/role",
                "desc"        => "获取角色拥有的权限",
                "permissions" => ["role:grant"]
            ],
            [
                "path"        => "/permission/all",
                "desc"        => "获取全部权限",
                "permissions" => ["permission:manage", "role:grant"]
            ],
            [
                "path"        => "/permission/grant",
                "desc"        => "角色授权",
                "permissions" => ["role:grant"]
            ],
            [
                "path"        => "/permission/category/exist",
                "desc"        => "判断权限分组是否存在",
                "permissions" => ["permission:update"]
            ],
            [
                "path"        => "/role/create",
                "desc"        => "添加角色",
                "permissions" => ["role:create"]
            ],
            [
                "path"        => "/role/update",
                "desc"        => "编辑角色",
                "permissions" => ["role:update"]
            ],
            [
                "path"        => "/role/delete",
                "desc"        => "删除角色",
                "permissions" => ["role:delete"]
            ],
            [
                "path"        => "/role/get",
                "desc"        => "获取全部角色",
                "permissions" => ["role:manage", "admin:create"]
            ],
            [
                "path"        => "/route/create",
                "desc"        => "添加接口",
                "permissions" => ["api:create"]
            ],
            [
                "path"        => "/route/update",
                "desc"        => "编辑接口",
                "permissions" => ["api:update"]
            ],
            [
                "path"        => "/route/delete",
                "desc"        => "删除接口",
                "permissions" => ["api:delete"]
            ],
            [
                "path"        => "/route/get",
                "desc"        => "获取全部接口",
                "permissions" => ["api:manage"]
            ]
        ];
        Route::truncate();
        DB::table('permission_route')->truncate();
        foreach ($apis as $api) {
            $newApi = Route::create(array_diff_key($api, ['permissions' => 1]));
            $permissions = Permission::where('guard_name', '=', $this->guardName)->whereIn('name', $api['permissions'])->get();
            $newApi->permissions()->sync($permissions->pluck('id'));
        }
    }
}
