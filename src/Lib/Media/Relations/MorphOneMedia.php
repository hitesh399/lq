<?php

namespace Singsys\LQ\Lib\Media\Relations;

use Singsys\LQ\Lib\Media\MediaUploader;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class MorphOneMedia extends MorphOne {

    /**
     * Store the Media File and update in relation.
     */
    public function addMedia(Array $file = null, $path = null, $thumbnails = null) {

        if ($file) {
            $media = null;
            if (isset($file['file']) && !empty($file['file'])) {
                $uploader = new MediaUploader($file, $path, $thumbnails);
                $data = $uploader->uploadAndPrepareData();
                $relation_array = $this->make()->toArray();
                $media = $this->updateOrCreate($relation_array, $data);
            } else if (isset($file['id']) && !empty($file['id'])) {
                $media = clone $this->getQuery();
                $media = $media->where('id', $file['id'])->first();
            }
            if($this->parent->mediaMorphRelation) {
                $this->parent->setRelation($this->parent->mediaMorphRelation, $media);
            }

        } else {
            $this->unlinkRelation();
            $this->parent->setRelation($this->parent->mediaMorphRelation, null);
        }
    }

    protected function unlinkRelation() {
        # here we also need to delete the Media File.
        $this->getQuery()->delete();
    }

    /**
     * Get the class name of the parent model.
     *
     * @return string
     */
    protected function setMediaType($type)
    {
        $this->parent->setMediaMorphType($type);
        $this->morphClass = $this->parent->getMorphClass();
        return $this;
    }
}
