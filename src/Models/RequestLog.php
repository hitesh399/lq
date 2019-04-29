<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Passport;
use Config;


class RequestLog extends Model
{

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'route_name',
        'request_method',
        'client_id',
        'ip_address',
        'device_id',
        'user_id',
        'response_status',
        'status_code',
        'request_headers',
        'response_headers',
        'request',
        'response'
    ];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'url'=> 'string',
        'route_name'=> 'string',
        'request_method'=> 'string',
        'client_id'=> 'int',
        'ip_address'=> 'string',
        'device_id'=> 'int',
        'user_id'=> 'int',
        'response_status'=> 'string',
        'status_code'=> 'int',
        'request_headers'=> 'array',
        'response_headers'=> 'array',
        'request'=> 'array',
        'response' => 'array'
    ];

    /**
     * To get the request Device informations
     */
    public function device()
    {
        return $this->belongsTo(Config::get('lq.device_class', Device::class));
    }

    /**
     * To get the request Client Informartion
     */
    public function client()
    {
        return $this->belongsTo(Passport::clientModel(), 'client_id');
    }

    /**
     * To get the user who was requested.
     */
    public function user()
    {
        return $this->belongsTo(Config::get('auth.providers.users.model'));
    }
}
