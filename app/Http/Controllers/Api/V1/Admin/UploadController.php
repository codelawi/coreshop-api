<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function image(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'max:20480', 'mimes:jpeg,png,webp,jpg,heic,heif'],
            'folder' => ['sometimes', 'in:banners,categories'],
        ]);

        $folder = $request->input('folder', 'banners');
        $file = $request->file('image');
        $filename = $folder.'/'.Str::uuid().'.jpg';

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        $stored = $disk->put($filename, $file->get(), [
            'ContentType' => 'image/jpeg',
        ]);

        if (! $stored) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image.',
            ], 500);
        }

        $url = rtrim(config('filesystems.disks.s3.url'), '/').'/'.$filename;

        return response()->json([
            'success' => true,
            'data' => ['url' => $url],
        ]);
    }
}
