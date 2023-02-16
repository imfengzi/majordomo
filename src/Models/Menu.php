<?php

namespace Chaos\Majordomo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class Menu extends Model
{
    use NodeTrait, SoftDeletes;

    protected $hidden = ['deleted_at'];

    protected $fillable = ['name', 'path', 'sequence', 'permission_id', 'thumbnail', 'icon', 'guard_name', 'parent_id'];
}
