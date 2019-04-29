<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_public', 'title', 'description', 'encrypted', 'permission_group_id', 'limitations'
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name'=> 'string',
        'is_public'=> 'string',
        'title'=> 'string',
        'description'=> 'string',
        'permission_group_id'=> 'int',
        'limitations'=> 'json',
        'encrypted'=> 'json',
        'landing_pag' => 'string'
    ];

    /**
     * To get the permission group detail
     */
    public function permissionGroup() {
        return $this->belongsTo(PermissionGroup::class);
    }

    /**
     * To get the permission field data
     */
    public function permissionFields() {

        return $this->hasMany(PermissionField::class);
    }
}
