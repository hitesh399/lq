<?php

namespace Singsys\LQ\Lib\Media\Relations;

use Singsys\LQ\Lib\Media\MediaUploader;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MorphOneMedia extends MorphOne {

    use Concerns\MediaStoreUpdateRelation;

    private $uploadedFile = [];

    /**
     * Store the file in default storage and update the relation in media table.
     *
     * @param array  $files     [Array Structure should be [[file => Blob, id => if already added]] ]
     * @param string $path      [Destination path after Storage base path]
     * @param array $thumbnails [Thumbnails]
     *
     * @return $this
     */
    public function addMedia(Array $file = null, $path = null, $thumbnails = null) {
        $media = clone $this->getQuery();
        $media = $media->first();
        if ($media && isset($file['file']) && $file['file'] ) {
            $file['id'] = $media->id;
        }
        $this->uploadedFile = $this->_updloadFileUpdateRelation($file, $path, $thumbnails);
        if (!$this->uploadedFile) {
            $this->unlinkRelation();
        }
        if ($this->parent->mediaMorphRelation) {
            $this->parent->setRelation($this->parent->mediaMorphRelation, $this->uploadedFile);
        }
        return $this;
    }
    public function getMedia() {
        return $this->uploadedFile;
    }
}
