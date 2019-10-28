<?php

namespace Singsys\LQ\Lib\Media\Relations\Concerns;

use Illuminate\Database\Eloquent\Model;
use Singsys\LQ\Lib\Media\MediaUploader;
use Singsys\LQ\Lib\Media\DeleteMediaFile;
use Illuminate\Database\Eloquent\Collection;

trait MediaStoreUpdateRelation
{
    /**
     * To delete media file and media data from database.
     *
     * @param int $id [Media Table primary id]
     *
     * @return bool
     */
    public function deleteMedia($id)
    {
        $media = $this->getQuery()->where('id', $id)->first();
        if ($media) {
            $this->deleteFile($media);

            return $media->delete();
        }

        return false;
    }

    /**
     * To upload the file in scource directory and insert file info and relation in media table.
     *
     * @param array $file [Array Structure should be [file => Blob, id => if already added] ]
     *
     * @return \Illuminate\Database\Eloquent\Collection
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
                $this->deleteFile($media);
                $media->update($data);

                return $media;
            } else {
                return $this->create($data);
            }
        } elseif (isset($file['id']) && !empty($file['id'])) {
            $media = clone $this->getQuery();
            $media = $media->where('id', $file['id'])->first();
            if ($media) {
                return $media;
            }
        }
    }

    /**
     * To remove all record from media table base on relation.
     *
     * @return void|
     */
    protected function unlinkRelation()
    {
        //  here we also need to delete the Media File.
        $this->getQuery()->get()->map(
            function ($q) {
                $this->deleteFile($q);

                return $q;
            }
        );
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

    /**
     * Get the media collection.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getMedia()
    {
        return $this->uploadedFiles;
    }

    /**
     * To delete media file.
     *
     * @param Illuminate\Database\Eloquent\Model $media [Media model]
     *
     * @return self
     */
    protected function deleteFile(Model $media)
    {
        $media = new DeleteMediaFile($media);
        $media->delete();

        return $this;
    }

    /**
     * To delete media file in collection.
     *
     * @param Illuminate\Database\Eloquent\Collection $collections [Media collection]
     *
     * @return self
     */
    protected function deleteFiles(Collection $collections)
    {
        DeleteMediaFile::deleteCollection($collections);

        return $this;
    }
}
