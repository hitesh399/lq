<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use Singsys\LQ\Lib\Concerns\MakeNew;

class PermissionGroup extends Model
{
    use MakeNew;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description'
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name'=> 'string',
        'description'=> 'string'
    ];


}
