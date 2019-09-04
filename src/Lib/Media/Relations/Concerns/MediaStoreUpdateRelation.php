<?php

namespace Singsys\LQ\Lib\Media\Relations\Concerns;

use Singsys\LQ\Lib\Media\MediaUploader;

trait MediaStoreUpdateRelation {

    /**
     * To upload the file in scource directory and insert file info and relation in media table
     *
     * @param array $file [Array Structure should be [file => Blob, id => if already added] ]
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @return void
     */
    private function _updloadFileUpdateRelation($file, $path, $thumbnails)
    {
        if (isset($file['file']) && !empty($file['file'])) {
            $uploader = new MediaUploader($file, $path, $thumbnails);
            $data = $uploader->uploadAndPrepareData();
            $data = array_merge($data, $this->make()->toArray());
            if (isset($file['id']) && !empty($file['id'])) {
                $media = clone $this->getQuery();
                $media = $media->where('id', $file['id'])->first();
                $media->update($data);
                return $media;
            } else {
               return $this->create($data);
            }

        } else if (isset($file['id']) && !empty($file['id'])) {
            $media = clone $this->getQuery();
            $media = $media->where('id', $file['id'])->first();
            if ($media) {
               return $media;
            }
        }
    }

    /**
     * To remove all record from media table base on relation
     *
     */
    protected function unlinkRelation() {
        //  here we also need to delete the Media File.
        $this->getQuery()->delete();
    }

    /**
     * Get the class name of the parent model.
     *
     * @param string $type [To set the media type]
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
