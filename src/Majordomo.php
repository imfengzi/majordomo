<?php

namespace Chaos\Majordomo;

use Illuminate\Support\Facades\Route;

class Majordomo
{
    static function routes()
    {
        Route::prefix(config('majordomo.route_prefix'))->group(function () {

            Route::post('admin/login', [config('majordomo.controllers.admin'), 'login']);

            Route::middleware("auth:" . config('majordomo.guard_name'))->group(function () {

                Route::middleware('verifyPermission')->group(function () {

                    Route::prefix('admin')->group(function () {
                        $class = config('majordomo.controllers.admin');
                        Route::post('password/reset', [$class, 'resetPassword']);
                        Route::post('password/update', [$class, 'modifyPassword']);
                        Route::post('create', [$class, 'create']);
                        Route::post('delete', [$class, 'delete']);
                        Route::post('grant', [$class, 'grant']);
                        Route::post('get', [$class, 'get']);
                        Route::get('self', [$class, 'getCurrent']);
                        Route::get('logout', [$class, 'logout']);
                    });

                    Route::prefix('menu')->group(function () {
                        $class = config('majordomo.controllers.menu');
                        Route::post('create', [$class, 'create']);
                        Route::post('update', [$class, 'update']);
                        Route::post('delete', [$class, 'delete']);
                        Route::post('all', [$class, 'getAll']);
                        Route::post('own', [$class, 'getOwn']);
                    });


                    Route::prefix('permission')->group(function () {
                        $class = config('majordomo.controllers.permission');
                        Route::post('update', [$class, 'update']);
                        Route::post('create', [$class, 'create']);
                        Route::post('categories', [$class, 'getCategories']);
                        Route::post('category/update', [$class, 'updateCategory']);
                        Route::post('delete', [$class, 'delete']);
                        Route::post('own', [$class, 'getOwn']);
                        Route::post('role', [$class, 'getByRole']);
                        Route::post('all', [$class, 'get']);
                        Route::post('grant', [$class, 'grant']);
                        Route::post('category/exist', [$class, 'categoryExist']);
                    });

                    Route::prefix('role')->group(function () {
                        $class = config('majordomo.controllers.role');
                        Route::post('get', [$class, 'get']);
                        Route::post('create', [$class, 'create']);
                        Route::post('update', [$class, 'update']);
                        Route::post('delete', [$class, 'delete']);
                    });

                    Route::prefix('route')->group(function () {
                        $class = config('majordomo.controllers.route');
                        Route::post('get', [$class, 'get']);
                        Route::post('create', [$class, 'create']);
                        Route::post('update', [$class, 'update']);
                        Route::post('delete', [$class, 'delete']);
                    });

                });
            });
        });
    }
}
