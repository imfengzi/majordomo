<?php
return [
    /*
     * 错误密码可重试次数，超过次数账号锁定
     * 0及以下代表无限次
     */
    "retry_num" => 5,

    /*
     * 触发锁定后封禁时长，单位s
     */
    "login_limit_time" => 1200,


    /*
     * 是否允许账号同时登录
     */
    "allow_multiple_login" => false,

    /*
     * 路由前缀
     */
    "route_prefix" => 'backend',

    /*
     * 是否自动注册路由
     */
    "auto_register_routers" => true,

    /*
     * 处理类
     */
    "controllers" => [
        'admin' => \Chaos\Majordomo\Http\Controllers\AdminController::class,
        'menu' => \Chaos\Majordomo\Http\Controllers\MenuController::class,
        'permission' => \Chaos\Majordomo\Http\Controllers\PermissionController::class,
        'role' => \Chaos\Majordomo\Http\Controllers\RoleController::class,
        'route' => \Chaos\Majordomo\Http\Controllers\RouteController::class,
    ],

    /*
     * guard_name
     */
    "guard_name" => 'backend'

];

