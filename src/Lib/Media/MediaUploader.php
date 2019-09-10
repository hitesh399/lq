<?php

namespace Singsys\LQ\Lib\Media;

use Illuminate\Support\Facades\Storage;

class MediaUploader
{
    private $file = null;
    private $attribute = null;
    private $destination = 'uploads';
    private $thumbnails = null;
    /**
     * @file => [file => File, thumbnails=> ['file' => File]]
     */
    public function __construct(array $file, $destination = null, $thumbnails = null)
    {
        $this->file = $file;

        if ($destination) {
            $this->destination = $destination;
        }

        $this->thumbnails = $thumbnails;
    }

    public static function mediaInstance()
    {
        return config('lq.media_model_instance');
    }

    /**
     * Store the data in Database
     */
    public function storeInDB()
    {
        $this->uploadAndPrepareData();
        $media = $this->mediaInstance();
        return $media::updateOrCreate(['path' => $this->attribute['path']], $this->attribute);
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
        $file_org_name = $file->getClientOriginalName();
        $create_file_name = $this->getFileName($this->destination, $file_org_name);
        $file->storeAs($this->destination, $create_file_name);

        $image_size =(substr($file->getMimeType(), 0, 5) == 'image') ? getImageSize($file->getPathName()): null;
        $attribute = [];

        $attribute['path'] = $this->destination.'/'.$create_file_name;
        $attribute['type'] = $file->getMimeType();

        $attribute['info'] = [
            'size' => $file->getSize(),
            'dimension' => $image_size ? ['width' => $image_size[0], 'height' => $image_size[1] ] : null,
            'original_name' => $file_org_name
        ];
        return $attribute;
    }

    /**
    * This function provides a unique file name
    * @param String - Folder Path
    * @param String - File Name
    */

    public function getFileName($dir, $name)
    {
        $name = preg_replace('/[^a-zA-Z0-9\-\_\.]+/', '', $name);
        //print_r($dir.'/'.$name); var_dump(Storage::exists($dir.'/'.$name)); exit;
        if (Storage::exists($dir.'/'.$name)) {
            $fName = explode('.', $name);
            $ext = end($fName);
            $last_index = count($fName)-1;
            unset($fName[$last_index]);
            $f_name = implode('.', $fName);

            $f_name_arr = explode('_', $f_name);
            $last_num = end($f_name_arr);
            $last_index = count($f_name_arr)-1;
            unset($f_name_arr[$last_index]);

            if (is_numeric($last_num)) {
                $f_name_arr[$last_index] = $last_num+1;
            } else {
                $f_name_arr[$last_index] =  $last_num.'_1';
            }
            $f_name  = implode('_', $f_name_arr);
            $name = $f_name.'.'.$ext;

            return $this->getFileName($dir, $name);
        }

        return $name;
    }
}
