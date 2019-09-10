<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'name', 'parent_role_id', 'title', 'description', 'client_ids', 'choosable', 'landing_page', 'settings'
    ];

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'client_ids' => 'array',
        'settings' => 'array',
        'parent_role_id'=> 'int',
        'title'=> 'string',
        'name'=> 'string',
        'description'=> 'string',
        'choosable'=> 'string',
        'landing_page'=> 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * To get the all permission of a role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')->withPivot([
            'limitations','id'
        ])->using(Relations\RolePivot::class);
    }
    public function rolePermissionFields()
    {
        return $this->hasMany(RolePermissionField::class);
    }
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class)->with('permission');
    }
    /**
     * To get the all permission fields of as role.
     */
    public function permissionFields()
    {
        return $this->belongsToMany(RolePermission::class, 'role_permission_fields', 'role_id', 'role_permission_id')->withPivot([
            'permission_field_id','id','permission_id', 'authority'
        ]);
    }
}
