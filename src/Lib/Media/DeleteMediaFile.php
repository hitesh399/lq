<?php

namespace Singsys\LQ\Lib\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class DeleteMediaFile
{
    private $_media = null;

    public function __construct(Model $media)
    {
        $this->_media = $media;
    }

    /**
     * To delete Media File.
     *
     * @return void|
     */
    public function delete()
    {
        $path = $this->_media->getOriginal('path');
        if ($this->_media->driver) {
            Storage::disk($this->_media->driver)->delete($path);
        } else {
            Storage::delete($path);
        }
    }

    /**
     * Create a new media delete instance.
     *
     * @param Illuminate\Database\Eloquent\Collection $collection [Media collection]
     *
     * @return self
     */
    public static function deleteCollection(Collection $collection)
    {
        foreach ($collection as $media) {
            $delete_media = new static($media);
            $delete_media->delete();
        }

        return $this;
    }
}
