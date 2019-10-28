<?php

namespace Singsys\LQ\Lib\Media\Relations;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MorphManyMedia extends MorphMany
{
    use Concerns\MediaStoreUpdateRelation;

    private $uploadedFiles = [];

    /**
     * Store the file in default storage and update the relation in media table.
     *
     * @param array  $files      [Array Structure should be [[file => Blob, id => if already added]] ]
     * @param string $path       [Destination path after Storage base path]
     * @param bool   $detach     [false if don't want to remove previous uploaded files.]
     * @param array  $thumbnails [Thumbnails]
     *
     * @return $this
     */
    public function addMedia(array $files = null, $path = null, $thumbnails = null, $detach = true)
    {
        $this->uploadedFiles = new Collection();

        if ($files && !empty($files)) {
            $current_ids = [];
            foreach ($files as $file) {
                $media = $this->_updloadFileUpdateRelation($file, $path, $thumbnails);
                if ($media) {
                    $this->uploadedFiles->push($media);
                    $current_ids[] = $media->id;
                }
            }
            if (count($current_ids) && $detach) {
                $unlinked = clone $this->getQuery();
                $old_files = $unlinked->whereNotIn('id', $current_ids)->get();
                $unlinked->whereNotIn('id', $current_ids)->delete();
                foreach ($old_files as $old_file) {
                    $this->deleteFile($old_file);
                }
            }
            if ($this->parent->mediaMorphRelation) {
                $this->parent->setRelation(
                    $this->parent->mediaMorphRelation, $this->uploadedFiles
                );
            }
        } else {
            $this->unlinkRelation();
            $this->parent->setRelation($this->parent->mediaMorphRelation, null);
        }

        return $this;
    }

    /**
     * To attach the media in this relation.
     *
     * @param array $ids [Media table primary keys]
     *
     * @return self
     */
    public function sync(array $ids)
    {
        $old_media = clone $this->getQuery();
        if (count($ids)) {
            $old_media->whereNotIn('id', $ids);
        }
        $this->deleteFiles($old_media->get());
        $old_media->delete();

        if (count($ids) === 0) {
            return $this;
        }

        $media_model = \Config::get('lq.media_model_instance');
        $new_media = new $media_model();

        $new_media = $new_media->whereIn('id', $ids);
        $new_media->update($this->make()->toArray());

        if ($this->parent->mediaMorphRelation) {
            $this->parent->setRelation(
                $this->parent->mediaMorphRelation, $new_media->get()
            );
        }

        return $this;
    }
}
