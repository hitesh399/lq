<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MultipleFileUploadTest extends TestCase
{
    public function test_no_file_uploading_null_data()
    {
        $user = User::first();
        $user->photos()->addMedia(null);
        $new_photos = $user->photos()->get();
        $this->assertTrue($new_photos->isEmpty());
    }

    public function test_upload_some_files()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $files = [];
        for ($i = 0; $i < 3; ++$i) {
            $files[] = ['file' => $file];
        }
        $user = User::first();
        $user->photos()->addMedia($files);
        $new_photos = $user->photos()->get();
        $this->assertTrue($new_photos->count() === 3);
        foreach ($new_photos as $new_photo) {
            Storage::exists($new_photo->getOriginal('path'));
        }
    }

    public function test_some_files_present_send_same_data()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $files = [];
        for ($i = 0; $i < 3; ++$i) {
            $files[] = ['file' => $file];
        }
        $user = User::first();
        $user->photos()->addMedia($files);
        $old_photos = $user->photos()->get();

        $files = [];
        foreach ($old_photos as $photo) {
            $files = ['id' => $photo->id, 'file' => null, 'path' => $photo->path];
        }
        $user->photos()->addMedia($files);
        $new_photos = $user->photos()->get();
        $this->assertTrue($old_photos->count() === $new_photos->count());
        foreach ($new_photos as  $new_photo) {
            $has_id = $old_photos->where('id', $new_photo->id)->first();
            $has_path = $old_photos->where('path', $new_photo->path)->first();
            $this->assertTrue($has_id ? true : false);
            $this->assertTrue($has_path ? true : false);
        }
        foreach ($old_photos as $old_photo) {
            $this->assertTrue(Storage::exists($old_photo->getOriginal('path')));
        }
    }

    public function test_some_files_present_deleting_a_file()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $files = [];
        for ($i = 0; $i < 3; ++$i) {
            $files[] = ['file' => $file];
        }
        $user = User::first();
        $user->photos()->addMedia($files);
        $old_photos = $user->photos()->get();
        $first_file = $old_photos->first();
        $files = [];
        foreach ($old_photos->slice(1) as $photo) {
            $files[] = ['id' => $photo->id, 'file' => null, 'path' => $photo->path];
        }
        $user->photos()->addMedia($files);
        $new_photos = $user->photos()->get();

        $this->assertTrue($new_photos->count() === 2);
        $has_first_item = $new_photos->where('id', $first_file->id)->first();
        $this->assertTrue(!$has_first_item ? true : false);
        foreach ($new_photos as $new_photo) {
            $this->assertTrue(Storage::exists($new_photo->getOriginal('path')));
        }
        $this->assertTrue(!Storage::exists($first_file->getOriginal('path')));
    }

    public function test_some_files_present_deleting_a_file_index_contain_null_data_in_key()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $files = [];
        for ($i = 0; $i < 3; ++$i) {
            $files[] = ['file' => $file];
        }
        $user = User::first();
        $user->photos()->addMedia($files);
        $old_photos = $user->photos()->get();
        $first_file = $old_photos->first();

        $files = [];
        $files[] = ['id' => null, 'file' => null];
        foreach ($old_photos->slice(1) as $photo) {
            $files[] = ['id' => $photo->id, 'file' => null, 'path' => $photo->path];
        }
        $user->photos()->addMedia($files);
        $new_photos = $user->photos()->get();

        $this->assertTrue($new_photos->count() === 2);
        $has_first_item = $new_photos->where('id', $first_file->id)->first();
        $this->assertTrue(!$has_first_item ? true : false);

        $this->assertTrue(!Storage::exists($first_file->getOriginal('path')));
        foreach ($new_photos as $new_photo) {
            $this->assertTrue(Storage::exists($new_photo->getOriginal('path')));
        }
    }

    public function test_some_files_present_changing_a_file()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $files = [];
        for ($i = 0; $i < 3; ++$i) {
            $files[] = ['file' => $file];
        }
        $user = User::first();
        $user->photos()->addMedia($files);
        $old_photos = $user->photos()->get();
        $first_photo = $old_photos->first();
        $files = [];
        $files[] = ['id' => $first_photo->id, 'file' => $file];
        foreach ($old_photos->slice(1) as $photo) {
            $files[] = ['id' => $photo->id, 'file' => null, 'path' => $photo->path];
        }
        $user->photos()->addMedia($files);
        $new_photos = $user->photos()->get();

        $this->assertTrue($new_photos->count() === $old_photos->count());

        $has_first_file_path = $new_photos->where('path', $first_photo->path)->first();
        $this->assertTrue(!$has_first_file_path ? true : false);

        foreach ($new_photos as $new_photo) {
            $this->assertTrue(Storage::exists($new_photo->getOriginal('path')));
        }
        $this->assertTrue(!Storage::exists($first_photo->getOriginal('path')));
    }

    /**
     * Accepted result old file should be present and new file should add.
     */
    public function test_some_file_preset_add_more_file()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $files = [];
        for ($i = 0; $i < 3; ++$i) {
            $files[] = ['file' => $file];
        }
        $user = User::first();
        $user->photos()->addMedia($files);
        $old_photos = $user->photos()->get();

        $files = [];
        for ($i = 0; $i < 3; ++$i) {
            $files[] = ['file' => $file];
        }

        $user->photos()->addMedia($files, 'test', null, false);
        $new_photos = $user->photos()->get();
        $this->assertTrue($new_photos->count() === ($old_photos->count() + count($files)));

        foreach ($new_photos as $new_photo) {
            $this->assertTrue(Storage::exists($new_photo->getOriginal('path')));
        }
        foreach ($old_photos as $old_photo) {
            $this->assertTrue(Storage::exists($old_photo->getOriginal('path')));
        }
    }

    public function test_delete_all_file()
    {
        $user = User::first();
        $old_photos = $user->photos()->get();

        $user->photos()->addMedia([]);
        $new_photos = $user->photos()->get();
        $this->assertTrue($new_photos->isEmpty());
        foreach ($old_photos as $old_photo) {
            $this->assertTrue(!Storage::exists($old_photo->getOriginal('path')));
        }
    }

    public function test_delete_file_one_by_one()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 600, 600);
        $files = [];
        for ($i = 0; $i < 3; ++$i) {
            $files[] = ['file' => $file];
        }
        $user = User::first();
        $user->photos()->addMedia($files);
        $old_photos = $user->photos()->get();
        foreach ($old_photos as $old_file) {
            $user->photos()->deleteMedia($old_file->id);
            $has_in_db = $user->photos()->where('id', $old_file->id)->first();
            $this->assertTrue(!$has_in_db);
            $this->assertTrue(!Storage::exists($old_file->getOriginal('path')));
        }
    }
}
