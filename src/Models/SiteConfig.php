<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    public $table = 'site_config';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','data', 'config_group', 'options'
    ];

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'name'=> 'string',
        'data'=> 'string',
        'config_group'=> 'string',
        'options'=> 'json',
    ];
}
