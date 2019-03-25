<?php
namespace cvexa\finder\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Redirect;

class FinderController extends Controller
{
    public function index()
    {
        $publicDirectories = Storage::disk('publicDisk')->allDirectories();
        return view('finder::finder', ['publicDirectories' =>$publicDirectories]);
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            'search' => 'required|string',
            'extensions' => 'nullable|string',
            'filter' => 'numeric|min:0|max:1',
        ]);
        $disk = 'publicDisk';
        $publicDirectories = $this->getDirectories($disk);
        $deny = Str::startsWith($request->path, '/ ');
        $denyVendor = Str::startsWith($request->path, '/vendor');
        if ($deny || $denyVendor) {
            return view('finder::finder', ['publicDirectories' =>$publicDirectories])->withErrors(['This route is denied!']);
        }
        $onlyFiles = false;
        $filter = (int)$request->filter;
        $output = [];

        if (!empty($request->path)) {
            $newPath = str_replace(' ', '', $request->path);
            app()->config["filesystems.disks.SearchPath"] = [
                'driver' => 'local',
                'root' => base_path().$newPath
            ];
            $disk = 'SearchPath';

            $publicDirectories = $this->getDirectories($disk);
            $dir = $publicDirectories;
            if (count($publicDirectories) < 1) {
                $dir = $this->getFiles($disk, null);
                $onlyFiles = true;
            }
        }

        if (is_numeric($request->location) && empty($request->path)) {
            $dir = $publicDirectories;
        } elseif (!is_numeric($request->location)) {
            $dir = [$request->location];
        }

        foreach ($dir as $folders) {
            $paths = $this->getFiles($disk, $onlyFiles?null:$folders);
            foreach ($paths as $file) {
                $content = $this->getFile($disk, $file);
                if ($filter > 0) {
                    $contentSearch = $this->nameSearch($file, $request->search, $request->extensions);
                } else {
                    $contentSearch = $this->contentSearch($file, $content, $request->search, $request->extensions);
                }
                $path = !empty($request->path)?base_path().$request->path.'/'.$file:public_path().'/'.$file;

                if ($contentSearch && !in_array($path, $output)) {
                    $output[] = $path;
                }
            }
        }
        $browse = Storage::disk($disk)->allDirectories();
        return view('finder::finder', [
            'output' => $output,
            'publicDirectories' => $browse,
            'searched' => $request->search,
            'extensions' => $request->extensions,
            'filter' => $request->filter,
            'customPath' => isset($newPath)?$newPath:false,
        ]);
    }

    public function getDirectories($disk)
    {
        return Storage::disk($disk)->allDirectories();
    }

    public function getFiles($disk, $folders = null)
    {
        if (!is_null($folders)) {
            return Storage::disk($disk)->allFiles($folders);
        }
        return Storage::disk($disk)->allFiles();
    }

    public function getFile($disk, $file)
    {
        return Storage::disk($disk)->get($file);
    }

    public function nameSearch($file, $keyword, $extensions)
    {
        if (!empty($extensions) || !is_null($extensions)) {
            $valid = $this->extensionsSearch($file, $extensions);
            if ($valid && str_contains($file, $keyword) && mb_strpos($file, $keyword) !== false && Str::contains($file, $keyword)) {
                $url = base_path().Storage::url($file);
                return $url;
            }
            return false;
        } else {
            if (str_contains($file, $keyword) && mb_strpos($file, $keyword) !== false && Str::contains($file, $keyword)) {
                $url = base_path().Storage::url($file);
                return $url;
            }
            return false;
        }
    }


    public function contentSearch($file, $content, $keyword, $extensions)
    {
        if (!empty($extensions) || !is_null($extensions)) {
            $valid = $this->extensionsSearch($file, $extensions);
            if ($valid && str_contains($content, $keyword) && mb_strpos($content, $keyword) !== false && Str::contains($content, $keyword)) {
                $url = base_path().Storage::url($file);
                return $url;
            }
            return false;
        } else {
            if (str_contains($content, $keyword) && mb_strpos($content, $keyword) !== false && Str::contains($content, $keyword)) {
                $url = base_path().Storage::url($file);
                return $url;
            }
            return false;
        }
    }

    public function extensionsSearch($file, $extensions)
    {
        $extensions = str_replace(' ', '', $extensions);
        $extensions = explode(',', $extensions);
        $valid = Str::endsWith($file, $extensions);
        return $valid;
    }
}
