<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'device_id', 'device_token', 'info', 'client_id'
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'device_id' => 'string',
        'client_id' => 'int',
        'device_token' => 'string',
        'info' => 'array',
    ];
}
