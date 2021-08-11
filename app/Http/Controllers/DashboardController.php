<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileTree;
use App\Services\StorageService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as FileUtil;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * ユーザーがアップロードしたフォルダやファイルの一覧
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $postUri = '')
    {
        if ($postUri) {
            $uri = DashboardController::decryptLink($postUri);
            // $files = Storage::disk('s3')->files("/{$uri}");
            // foreach ($files as $file) {
            //     $image = Storage::disk('s3')->get($file);
            // }
            // Storage::disk('s3')->directories("/{$uri}");
        }

        $progress = new \stdClass();
        $progress->usedVolumeSize = 7;
        $progress->volumeSize = 10;

        $files = [];
        $file = new \stdClass();
        $file->name = 'sample1.mp4';
        $file->path = 'public/images/sample1';
        $files[] = $file;

        $file = new \stdClass();
        $file->name = 'sample2.mp4';
        $file->path = 'public/images/sample2';
        $files[] = $file;

        $file = new \stdClass();
        $file->name = 'sample3.mp4';
        $file->path = 'public/images/sample3';
        $files[] = $file;

        $file = new \stdClass();
        $file->name = 'sample4.mp4';
        $file->path = 'public/images/sample4';
        $files[] = $file;

        $file = new \stdClass();
        $file->name = 'sample5.mp4';
        $file->path = 'public/images/sample5';
        $files[] = $file;

        $user_id = Auth::id();
        $disk = Storage::disk('s3');
        $directories = $disk->directories($user_id);
        $tree = new FileTree($user_id);
        $this->buildDirectoryTree($disk, $tree, $directories);

        return Inertia::render(
            'Dashboard',
            [
                'progress' => $progress,
                'folderTree' => $tree,
                'files' => $files,
                'postUri' => $postUri
            ]
        );
    }

    public function buildDirectoryTree(Filesystem $disk, FileTree &$root, array $directories)
    {
        foreach ($directories as $directory) {
            $tree = new FileTree($directory);
            if (count($disk->directories($directory)) > 0) {
                $this->buildDirectoryTree($disk, $tree, $disk->directories($directory));
            }
            array_push($root->nodes, $tree);
        }
    }

    /**
     * Upload files to aws s3
     */
    public function uploadFiles(Request $request)
    {
        // $service = new StorageService();

        $postUri = $request['postUri'] == null ? '/' . Auth::id() : DashboardController::decryptLink($request['postUri']);
        foreach ($request['files'] as $inputFile) {
            $file = $inputFile['file'];
            $fileName = $inputFile['name'];
            $fileBytes = $inputFile['bytes'];
            $fileType = $inputFile['type'];
            $relativePath = str_replace($fileName, '', $inputFile['relativePath']);
            $fileExtension = FileUtil::extension($fileName);

            $url = Storage::disk('s3')->put("/{$postUri}/{$relativePath}", $file);
            // Log::debug($url);
            // $path = $file->storeAs("/{$user_id}", $fileName, 's3');
            // if (isset($url)) {
            //     if (isset($relativePath)) {
            //         $dir = Directory::firstOrCreate([
            //             'user_id' => $user_id,
            //             'full_path' => '/' . $relativePath,
            //             'name' => $relativePath,
            //         ]);
            //     }
            //     if (isset($dir)) {
            //         File::create([
            //             'directory_id' => $dir->id,
            //             'full_path' => $url,
            //             'name' => $fileName,
            //             'bytes' => $fileBytes,
            //             'extension' => $fileExtension
            //         ]);
            //     }
            // }
        }

        $uri = $request['postUri'];
        return Redirect::to("/dashboard/{$uri}");
    }

    public static function encriptLink($link)
    {
        return bin2hex(openssl_encrypt($link, 'AES-128-ECB', 'passkey'));
    }

    public static function decryptLink($link)
    {
        return openssl_decrypt(hex2bin($link), 'AES-128-ECB', 'passkey');
    }
}
