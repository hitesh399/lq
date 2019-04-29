<?php
namespace Singsys\LQ\Lib\Media\Relations;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Singsys\LQ\Lib\Media\MediaUploader;

class HasOneMedia extends HasOne {

    /**
     * Store the Media File and update in relation.
     */
    public function addMedia(Array $file, $path = null) {

        $uploader = new MediaUploader($file, $path);
        $media = $uploader->storeInDB();
        return $this->parent->update([$this->getForeignKeyName() => $media->id]);
    }
}
