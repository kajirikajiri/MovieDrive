<?php

namespace App\Http\Controllers;

use App\Models\FileContent;
use App\Models\FileTree;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request, $postUri = '')
    {
        if (!$postUri) {
            $user_id = FileContent::encriptLink(Auth::id());
            return Redirect::to("/dashboard/{$user_id}");
        }

        $progress = new \stdClass();
        $progress->usedVolumeSize = round(((int)FileContent::where('user_id', Auth::id())->sum('bytes')) / 1000 / 1000 / 1000, 2);
        $progress->volumeSize = 10;

        $uri = $postUri;
        $files = $this->getFiles(FileContent::decryptLink($uri));

        $tree = new FileTree(Auth::id());
        $this->buildDirectoryTree(Storage::disk(), $tree, $this->getDirectories(Auth::id()));

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

    private function getFiles(string $uri)
    {
        $files = FileContent::where('user_id', Auth::id())->where('dirname', $uri)->get();
        return $files;
    }

    public function uploadFiles(Request $request)
    {
        $postUri = $request['postUri'];
        $uri = FileContent::decryptLink($postUri);
        $files = $request->file('files');
        Log::debug("upload files:", ['user' => Auth::id(), 'files' => $files]);
        foreach ($request['files'] as $i => $input) {
            $file = $files[$i]['file'];
            $dirname = str_replace($input['name'], '', $input['relativePath']);
            $path = $file->store(FileContent::combinePath($uri, $dirname), 'public');
            $pathInfo = pathinfo($path);
            $content = new FileContent();
            $content->fill([
                'user_id' => Auth::id(),
                'dirname' => $pathInfo['dirname'],
                'displayname' => $input['name'],
                'savedname'  => $pathInfo['basename'],
                'link' => '/storage/' . $path,
                'extension' => $pathInfo['extension'],
                'bytes' => (int)$input['bytes'],

            ]);
            $result = $content->save();
            if ($result) {
                Log::debug('insert to file_content:', ['result' => $result, 'content' => $content]);
            } else {
                Log::warning('delete tmp file:', ["content" => $content]);
                Storage::delete($path);
            }
        }
        return Redirect::to("/dashboard/{$postUri}");
    }

    private function buildDirectoryTree(Filesystem $disk, FileTree &$root, array $directories)
    {
        foreach ($directories as $directory) {
            $tree = new FileTree($directory);
            Log::debug('search directories', [$disk->directories($directory)]);
            if (count($disk->directories($directory)) > 0) {
                $this->buildDirectoryTree($disk, $tree, $disk->directories($directory));
            }
            array_push($root->nodes, $tree);
        }
    }

    private function getDirectories(string $postUri)
    {
        $directories = Storage::allDirectories('public/' . $postUri);
        Log::debug('get directories', ['uri' => 'public/' . $postUri, 'dirs' => $directories]);
        return $directories;
    }
}
