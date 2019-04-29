<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermissionField extends Model
{
    /**
     * Get permission fields Detail.
     */
    function permissionFields() {

        return $this->hasOne(PermissionField::class, 'id', 'permission_field_id');
    }
}
