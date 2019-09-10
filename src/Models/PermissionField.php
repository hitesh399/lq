<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionField extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'permission_id', 'title', 'client_field', 'table_columns'
    ];
    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'permission_id'  => 'integer',
        'title'         => 'string',
        'client_field'       => 'string',
        'table_columns'       => 'json'
    ];
    /**
     * Get the Permission information
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
