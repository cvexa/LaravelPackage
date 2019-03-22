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
                if (str_contains($content, $request->search) && mb_strpos($content, $request->search) !== false && Str::contains($content, $request->search)) {
                    $url = storage_path($file);
                    if (!in_array($url, $output)) {
                        $output[] = $url;
                    }
                }
            }
        }
        return view('finder::finder', ['output' => $output,'publicDirectories' => $publicDirectories]);
    }
}
