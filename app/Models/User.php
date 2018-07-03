<?php

namespace App\Models;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $table='oss_user';

    protected $fillable = ['user', 'name', 'last_ip','last_login_time'];

    /**
     * 用户角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class,'admin_role_user','user_id','role_id');
    }


    /**
     * 判断用户是否具有某个角色
     * @param $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return !!$role->intersect($this->roles)->count();
    }


    /**
     * 判断用户是否具有某权限
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name',$permission)->first();
            if (!$permission) return false;
        }

        return $this->hasRole($permission->roles);
    }

    /**
     * 给用户分配角色
     * @param $role
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function assignRole($role)
    {
        return $this->roles()->save($role);
    }


    /**
     * 角色整体添加与修改
     * @param array $RoleId
     * @return bool
     */
    public function giveRoleTo(array $RoleId){
        $this->roles()->detach();
        $roles= Role::whereIn('id',$RoleId)->get();
        foreach ($roles as $v){
            $this->assignRole($v);
        }
        return true;
    }
}
