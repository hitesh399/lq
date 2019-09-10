<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','subject', 'body', 'options','type'
    ];

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'name'=> 'string',
        'subject'=> 'string',
        'type'=> 'string',
        'body'=> 'string',
        'options'=> 'json',
    ];
}
