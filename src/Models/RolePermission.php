<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{

    function rolePermissionFields() {

        return $this->hasMany(RolePermissionField::class, 'role_permission_id');
    }
    /**
     * Get the Permission information
     */
    public function permission() {
        return $this->belongsTo(Permission::class);
    }
}
