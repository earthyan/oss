<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table='oss_permissions';

    public function roles()
    {
        return $this->belongsToMany(Role::class,'oss_permission_role','permission_id','role_id');
    }

}
