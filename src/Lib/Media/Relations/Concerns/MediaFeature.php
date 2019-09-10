<?php

namespace Singsys\LQ\Lib\Media\Relations\Concerns;

use Singsys\LQ\Lib\Media\MediaUploader;

trait MediaFeature
{

    /**
     * Store the Media File and update in relation.
     */
    public function addMedia(array $files, $path = null, $should_update = true)
    {
        if (!isset($files[0])) {
            $files = [$files];
        }
        $mediaIns = config('lq.media_model_instance');

        $media_data = [];
        $uploaded_data = [];
        foreach ($files as $file) {
            if (isset($file['file'])) {
                $uploader = new MediaUploader($file, $path);
                $media = $uploader->storeInDB();
                $media_data[] = $media->id;
                $uploaded_data[] = $media;
            } else {
                $media_data[] = (int)$file['id'];
                $uploaded_data[] = $mediaIns::find($file['id']);
            }
        }

        $isJson = $this->parent->hasCast($this->getForeignKey(), ['array', 'json', 'object', 'collection']);
        $data = $isJson ? $media_data : $media_data[0];
        $uploaded_data = $isJson ? $uploaded_data : $uploaded_data[0];

        $this->parent->setAttribute($this->getForeignKey(), $data);
        //$this->parent->setTouchedRelations($this->relation]);

        if ($should_update) {
            $this->parent->update([$this->getForeignKey() => $data]);
        }

        return $this;
    }
}
