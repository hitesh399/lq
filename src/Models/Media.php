<?php

namespace Singsys\LQ\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'type', 'thumbnails', 'info', 'mediable_type', 'mediable_id', 'user_id', 'driver',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'thumbnails' => 'array',
        'info' => 'array',
        'user_id' => 'int',
        'mediable_id' => 'int',
        'path' => 'string',
        'mediable_type' => 'string',
        'type' => 'string',
    ];

    public function getPathAttribute($path)
    {
        if ($public_url = $this->getPublicUrl()) {
            return $public_url;
        }

        return $path ? (
            $this->getAttribute('driver') ? \Storage::disk($this->getAttribute('driver'))->url($path) : \Storage::url($path)
        ) : null;
    }

    public function getPublicUrl()
    {
        $info = $this->getAttribute('info');
        if (isset($info['public_url']) && !empty($info['public_url'])) {
            return $info['public_url'];
        }

        return null;
    }
}
