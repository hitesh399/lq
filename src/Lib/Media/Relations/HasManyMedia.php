<?php
namespace Singsys\LQ\Lib\Media\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Singsys\LQ\Lib\Media\MediaUploader;

class HasManyMedia extends HasMany
{

    /**
     * Store the Media File and update in relation.
     */
    public function addMedia(array $file, $path = null)
    {
        $uploader = new MediaUploader($file, $path);
        $media = $uploader->storeInDB();
        return $this->parent->update([$this->getForeignKeyName() => $media->id]);
    }
}
