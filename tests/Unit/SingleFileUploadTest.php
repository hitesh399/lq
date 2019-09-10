<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SingleFileUploadTest extends TestCase
{
    /**
     * When file is not present and uploading null data
     *
     * @excepted_result no exception throw
     */
    public function test_blank_data()
    {
        $user = User::first();
        $user->profileImage()->addMedia(null);
        $media = $user->profileImage()->first();
        $this->assertTrue(!$media ? true : false);
    }
    /**
     * WHen file is present and uploading null data
     *
     * @excepted_result Media primary if should no be changed and new path should be link.
     */
    public function test_already_has_file_and_uplloading_blank_data()
    {
        $user = User::first();
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $user->profileImage()->addMedia(['file' => $file]);
        $old_file = $user->profileImage()->first();

        $user->profileImage()->addMedia(null);
        $media = $user->profileImage()->first();
        $this->assertTrue(!$media ? true : false);
        $this->assertTrue(!Storage::exists($old_file->getOriginal('path')));
    }

    /**
     * When  file is present and uploading new file.
     */
    public function test_change_file()
    {
        $user = User::first();
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $user->profileImage()->addMedia(['file' => $file]);
        $old_mdeia = $user->profileImage()->first();

        /**
         * Uploading new File.
         */
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $user->profileImage()->addMedia(['file' => $file]);
        $new_file = $user->profileImage()->first();
        $this->assertTrue(($old_mdeia->id == $new_file->id && $old_mdeia->path != $new_file->path));
        $this->assertTrue(!Storage::exists($old_mdeia->getOriginal('path')));
        $this->assertTrue(Storage::exists($new_file->getOriginal('path')));
    }
    /**
     * When file is present and sending null data in file key and old media id in id key
     * [file => null, id => 12]
     *
     * @excepted_result media file should not delete.
     */
    public function test_file_present_null_in_file_key_old_media_id_in_id_key()
    {
        $user = User::first();
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $user->profileImage()->addMedia(['file' => $file]);
        $old_mdeia = $user->profileImage()->first();

        $user->profileImage()->addMedia(['file' => null, 'id' => $old_mdeia->id]);
        $media = $user->profileImage()->first();
        $this->assertTrue(($media && $media->id === $old_mdeia->id));
        $this->assertTrue(Storage::exists($old_mdeia->getOriginal('path')));
    }
    /**
     * When file is present and sending null in file and id
     * [file => null, id => null]
     *
     * @excepted_result media file should delete.
     */
    public function test_file_present_null_in_file_key()
    {
        $user = User::first();
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $user->profileImage()->addMedia(['file' => $file]);
        $old_mdeia = $user->profileImage()->first();

        $user->profileImage()->addMedia(['file' => null, 'id' => null]);
        $media = $user->profileImage()->first();
        $this->assertTrue(!$media ? true : false);
        $this->assertTrue(!Storage::exists($old_mdeia->getOriginal('path')));
    }
}
