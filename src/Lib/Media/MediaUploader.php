<?php

namespace Singsys\LQ\Lib\Media;

use Illuminate\Support\Facades\Storage;

class MediaUploader
{
    private $file = null;
    private $attribute = null;
    private $destination = null;
    private $thumbnails = null;
    private $_driver = null;

    /**
     * @file => [file => File, thumbnails=> ['file' => File]]
     */
    public function __construct(array $file, $destination = null, $thumbnails = null, $drive = null)
    {
        $this->file = $file;
        $this->_driver = $drive ? $drive : \Config::get('filesystems.default', 'public');

        if ($destination) {
            $this->destination = $destination;
        }

        $this->thumbnails = $thumbnails;
    }

    public static function mediaInstance()
    {
        $model = config('lq.media_model_instance');

        return new $model();
    }

    /**
     * Store the data in Database.
     */
    public function storeInDB($id = null)
    {
        $this->uploadAndPrepareData();
        $media = $this->mediaInstance();
        if ($id) {
            $media = $media->find($id);
            $delete_media = new DeleteMediaFile($media);
            $delete_media->delete();
            $media->update($this->attribute);

            return $media;
        }

        return $media->create($this->attribute);
    }

    /**
     * Upload the given file and prepare data.
     */
    public function uploadAndPrepareData()
    {
        $file = $this->file['file'];
        $thumbnails = isset($this->file['thumbnails']) ? $this->file['thumbnails'] : null;

        $this->attribute = $this->upload($file);

        if ($thumbnails) {
            foreach ($thumbnails as $thumbnail) {
                $file = $thumbnail['file'];
                $this->attribute['thumbnails'][] = $this->upload($file);
            }
        }

        return $this->attribute;
    }

    /**
     * To Store the File at default disk.
     */
    public function upload($file)
    {
        $disk_info = \Config::get('filesystems.disks.'.$this->_driver);

        $file_org_name = $file->getClientOriginalName();
        $unique_fname = uniqid().'_'.time().'.'.$file->getClientOriginalExtension();
        $path = $this->destination ? $this->destination.'/'.$unique_fname : $unique_fname;
        $data = $file->storeAs($this->destination, $unique_fname, $this->_driver);

        $image_size = (
            substr($file->getMimeType(), 0, 5) == 'image'
        ) ? getimagesize($file->getPathName()) : null;
        $attribute = [];

        $attribute['path'] = $path;
        $attribute['type'] = $file->getMimeType();
        $attribute['driver'] = $this->_driver;
        $attribute['info'] = [
            'size' => $file->getSize(),
            'dimension' => $image_size ? ['width' => $image_size[0], 'height' => $image_size[1]] : null,
            'original_name' => $file_org_name,
        ];

        if (isset($disk_info['save_public_url']) && $disk_info['save_public_url']) {
            $attribute['info']['public_url'] = Storage::disk(
                $this->_driver
            )->url($path);
        }

        return $attribute;
    }
}
