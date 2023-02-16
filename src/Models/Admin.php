<?php

namespace Chaos\Majordomo\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;


class Admin extends Model
{
    use SoftDeletes, HasApiTokens, HasPermissions, HasRoles;

    protected $guarded = [];

    protected $hidden = ['password', 'deleted_at'];


    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($this->password, $password) || Hash::check($password, $this->password);
    }

    public function shouldLock()
    {
        return $this->retry_num >= 5;
    }

    public function isLocked()
    {
        return $this->lock_to && Carbon::now()->lessThan(Carbon::create($this->lock_to));
    }

    public function clearLock($autoSave = true)
    {
        if ($this->lock_at || $this->retry_num) {
            $this->retry_num = 0;
            $this->lock_to = null;
            if ($autoSave) {
                $this->save();
            }
        }
        return $this;
    }

    public function lockOnce()
    {
        $this->increment('retry_num', 1);
        if ($this->shouldLock()) {
            $this->lock_to = Carbon::now()->addSeconds(config('majordomo.login_limit_time'));
            $this->save();
        }
        return $this;
    }

    public function verifyPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    public static function randPassword($length = 16)
    {
        $randStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.$*@!#";
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $randStr[rand(0, strlen($randStr) - 1)];
        }
        return $password;
    }

    protected function password(): Attribute
    {
        return new Attribute(null, function ($v) {
            return bcrypt($v);
        });
    }

    public function isSuper()
    {
        return $this->username === 'admin';
    }
}
