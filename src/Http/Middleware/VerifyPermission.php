<?php

namespace Chaos\Majordomo\Http\Middleware;

use Chaos\Majordomo\Models\Route;
use Closure;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Exceptions\UnauthorizedException;

class VerifyPermission
{
    public function handle($request, Closure $next)
    {
        $admin = $request->user();
        if ($admin->isSuper()) {
            return $next($request);
        }
        $path = $request->route()->uri;
        $routePrefix = config('majordomo.route_prefix');
        $path = substr($path, strlen($routePrefix));

        $cacheKey = 'route.permission.' . md5($path);
        $permissions = Cache::rememberForever($cacheKey, function () use ($path) {
            $route = Route::where('path', $path)->with('permissions')->first();
            return $route ? $route->permissions->pluck('name')->toArray() : [];
        });
        if (count($permissions) > 0 && !$admin->hasAnyPermission($permissions)) {
            throw  UnauthorizedException::forPermissions($permissions);
        }
        return $next($request);
    }
}
