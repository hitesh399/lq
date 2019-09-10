<?php

namespace Singsys\LQ\Lib\Media\Relations;

use Singsys\LQ\Lib\Media\MediaUploader;
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
     * @param array  $files     [Array Structure should be [[file => Blob, id => if already added]] ]
     * @param string $path      [Destination path after Storage base path]
     * @param boolean $detach   [false if don't want to remove previous uploaded files.]
     * @param array $thumbnails [Thumbnails]
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
                    Storage::delete($old_file->getOriginal('path'));
                }
            }
            if ($this->parent->mediaMorphRelation) {
                $this->parent->setRelation($this->parent->mediaMorphRelation, $this->uploadedFiles);
            }
        } else {
            $this->unlinkRelation();
            $this->parent->setRelation($this->parent->mediaMorphRelation, null);
        }
        return $this;
    }
    public function getMedia()
    {
        return $this->uploadedFiles;
    }
    public function deleteMedia($id)
    {
        $media = $this->getQuery()->where('id', $id)->first();
        if ($media) {
            Storage::delete($media->getOriginal('path'));
            return $media->delete();
        }
        return false;
    }
}
