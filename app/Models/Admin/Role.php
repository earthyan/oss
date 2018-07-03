<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table='oss_roles';
    //
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'oss_permission_role','role_id','permission_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class,'oss_role_user','role_id','user_id');
    }
    //给角色添加权限
    public function givePermissionTo($permission)
    {
        return $this->permissions()->save($permission);
    }


}
