<?php

namespace Chaos\Majordomo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use SoftDeletes;

    protected $fillable = ['path', 'desc'];

    public static function booted()
    {
        static::deleted(function ($route) {
            $route->permissions()->detach();
        });
    }


    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }
}
