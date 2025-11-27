<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['module_id', 'name', 'display_name'];

    public function role_permissions(){
        return $this->hasMany(RolePermission::class, 'permission_id', 'id');
    }
}
