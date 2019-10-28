<?php

namespace Singsys\LQ\Lib\Media\Relations;

use Illuminate\Database\Eloquent\Relations\MorphOne;

class MorphOneMedia extends MorphOne
{
    use Concerns\MediaStoreUpdateRelation;

    private $uploadedFile = [];

    /**
     * Store the file in default storage and update the relation in media table.
     *
     * @param array  $files      [Array Structure should be [[file => Blob, id => if already added]] ]
     * @param string $path       [Destination path after Storage base path]
     * @param array  $thumbnails [Thumbnails]
     *
     * @return $this
     */
    public function addMedia(array $file = null, $path = null, $thumbnails = null)
    {
        $media = clone $this->getQuery();
        $media = $media->first();
        if ($media && isset($file['file']) && $file['file']) {
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

    /**
     * To attach the media in this relation.
     *
     * @param int $id [Media table primary keys]
     *
     * @return self
     */
    public function sync($id)
    {
        $media = clone $this->getQuery();
        $media = $media->first();
        /*
         * Delete Old File Is Exists
         */
        if ($media && (!$id || $media->id != $id)) {
            $this->deleteFile($media);
        }
        if ($id) {
            $media_model = \Config::get('lq.media_model_instance');
            $new_media = new $media_model();
            $new_media = $new_media->find($id);
            $new_media->update($this->make()->toArray());
            if ($this->parent->mediaMorphRelation) {
                $this->parent->setRelation(
                    $this->parent->mediaMorphRelation, $new_media
                );
            }
        } elseif ($media) {
            $media->delete();
        }

        return $this;
    }
}
