<?php
namespace cvexa\finder\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FinderController extends Controller
{
    public function index()
    {
        $publicDirectories = Storage::disk('publicDisk')->allDirectories();
        return view('finder::finder', ['publicDirectories' =>$publicDirectories]);
    }

    public function search(Request $request)
    {
        $publicDirectories = Storage::disk('publicDisk')->allDirectories();
        if (is_numeric($request->location)) {
            $dir = $publicDirectories;
        } else {
            $dir = [$request->location];
        }
        $output = [];
        foreach ($dir as $folders) {
            $paths = Storage::disk('publicDisk')->allFiles($folders);
            foreach ($paths as $file) {
                $content = Storage::disk('publicDisk')->get($file);
                $contentSearch = $this->contentSearch($content, $request->keyword, $request->extensions);
                if ($contentsSearch && !in_array($contentSearch, $output)) {
                    $output[] = $contentSearch;
                }
            }
        }
        return view('finder::finder', ['output' => $output,'publicDirectories' => $publicDirectories]);
    }


    public function contentSearch($content, $keyword, $extensions)
    {
        if (!empty($extensions) || !is_null($extensions)) {
            $valid = extensionsSearch($file, $extensions);
            if ($valid && str_contains($content, $keyword) && mb_strpos($content, $keyword) !== false && Str::contains($content, $keyword)) {
                $url = storage_path($file);
                return $url;
            }
            return false;
        } else {
            //if not extensions provided will search anyway of file extension
            if (str_contains($content, $keyword) && mb_strpos($content, $keyword) !== false && Str::contains($content, $keyword)) {
                $url = storage_path($file);
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
