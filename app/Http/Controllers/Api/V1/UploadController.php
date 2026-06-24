<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function avatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'file', 'max:10240', 'mimes:jpeg,png,webp,jpg,heic,heif'],
        ]);

        $file = $request->file('avatar');
        $filename = 'avatars/'.Str::uuid().'.jpg';

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        $stored = $disk->put($filename, $file->get(), [
            'ContentType' => 'image/jpeg',
        ]);

        if (! $stored) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar.',
            ], 500);
        }

        $url = rtrim(config('filesystems.disks.s3.url'), '/').'/'.$filename;

        return response()->json([
            'success' => true,
            'data' => ['url' => $url],
        ]);
    }
}
