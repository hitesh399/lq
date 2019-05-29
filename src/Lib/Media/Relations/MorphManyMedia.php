<?php

namespace Singsys\LQ\Lib\Media\Relations;

use Singsys\LQ\Lib\Media\MediaUploader;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MorphManyMedia extends MorphMany {

    /**
     * Store the Media File and update in relation.
     */
    public function addMedia(Array $files = null, $path = null, $thumbnails = null) {
        if ($files && !empty($files)) {
            $current_ids = [];
            $uploaded_files = [];
            foreach ($files as $file) {
                if (isset($file['file'])) {                   
                    $uploader = new MediaUploader($file, $path, $thumbnails);
                    $data = $uploader->uploadAndPrepareData();
                    $data = array_merge($data, $this->make()->toArray());
                    if (isset($file['id']) && !empty($file['id'])) {
                        $media = clone $this->getQuery();
                        $media = $media->where('id', $file['id'])->first();
                        $media->update($data);                        
                    } else {
                        $media = $this->create($data);
                    }

                    $uploaded_files[] = $media;
                    $current_ids[] = $media->id;

                } else if (isset($file['id']) && !empty($file['id'])) {
                    $media = clone $this->getQuery();
                    $media = $media->where('id', $file['id'])->first();
                    $uploaded_files[] = $media;
                    $current_ids[] = $file['id'];
                }                
            }

            if (count($current_ids)) {
                $unlinked = clone $this->getQuery();
                $unlinked->whereNotIn('id', $current_ids)->delete();
            }

            if($this->parent->mediaMorphRelation) {
                $this->parent->setAttribute($this->parent->mediaMorphRelation, $uploaded_files);
            }

        } else {
            $this->unlinkRelation();
            $this->parent->setRelation($this->parent->mediaMorphRelation, null);
        }
        return $this;
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
