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
        $timeStart = microtime(true);
        $data = $request->validate([
            'search' => 'required|string|max:500',
            'extensions' => 'nullable|string|max:500',
            'filter' => 'numeric|min:0|max:1',
            'path' => 'nullable|string|min:2',
            'outside' => 'nullable|string|min:2',
        ]);

        $disk = 'publicDisk';
        $publicDirectories = $this->getDirectories($disk);
        $filter = (int)$request->filter;
        $output = [];

        $denyVendor = Str::startsWith($request->path, '/vendor');
        if ($denyVendor) {
            return view('finder::finder', ['publicDirectories' =>$publicDirectories])->withErrors(['This route is denied!']);
        }

        if (!empty($request->path)) {
            $newPath = str_replace(' ', '', $request->path);
            app()->config["filesystems.disks.SearchPath"] = [
                'driver' => 'local',
                'root' => base_path().'/'.$newPath
            ];
            $disk = 'SearchPath';
            $dir = $this->getCustomDirs($disk);
        }

        if (!empty($request->outside)) {
            $disk = $this->outsideFolders($request->outside);
            $dir = $this->getCustomDirs($disk);
        }

        if (is_numeric($request->location) && empty($request->path) && empty($request->outside)) {
            $dir = Storage::disk('publicDisk')->allDirectories();
            $disk = 'publicDisk';
        } elseif (!is_numeric($request->location)) {
            $dir = [$request->location];
        }

        foreach ($dir as $folders) {
            $paths = $this->getFiles($disk);
            if (!is_numeric($request->location)) {
                $paths = $this->getFiles($disk, $folders);
            }
            foreach ($paths as $file) {
                $content = $this->getFile($disk, $file);
                if ($filter > 0) {
                    $contentSearch = $this->nameSearch($file, $request->search, $request->extensions);
                } else {
                    $contentSearch = $this->contentSearch($file, $content, $request->search, $request->extensions);
                }
                $path = $this->getPath($request->path, $request->outside, $file);

                if ($contentSearch && !in_array($path, $output)) {
                    $output[] = $path;
                }
            }
        }
        $browse = Storage::disk('publicDisk')->allDirectories();
        $timeEnd = number_format((microtime(true)-$timeStart), 3);
        return view('finder::finder', [
            'output' => $output,
            'publicDirectories' => $browse,
            'searched' => $request->search,
            'extensions' => $request->extensions,
            'filter' => $request->filter,
            'customPath' => isset($newPath)?$newPath:false,
            'timeEnd' => $timeEnd
        ]);
    }

    public function outsideFolders($path)
    {
        app()->config["filesystems.disks.outside"] = [
                    'driver' => 'local',
                    'root' => dirname(getcwd()). DIRECTORY_SEPARATOR . '../'.$path
                ];
        $disk = 'outside';

        return $disk;
    }

    public function getPath($custom, $outside, $file)
    {
        $path = public_path().'/'.$file;
        if (!empty($custom)) {
            $path = base_path().$custom.'/'.$file;
        }
        if (!empty($outside)) {
            $path = $outside.'/'.$file;
        }
        return $path;
    }

    public function getCustomDirs($disk)
    {
        $publicDirectories = $this->getDirectories($disk);
        $dir = $publicDirectories;
        if (count($dir) < 1) {
            $dir = $this->getFiles($disk);
        }
        return $dir;
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
