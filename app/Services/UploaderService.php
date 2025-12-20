<?php

namespace App\Services;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Image;

class UploaderService
{
    public function uploadImage(UploadedFile $file, string $path, bool $applyCut = false, int $width = 200, int $height = 200): array
    {
        $name = $this->generateRandomString() . "_" . time() . "." . $file->getClientOriginalExtension();

        try {
            $img = Image::make($file->getRealPath());

            if ($applyCut) {
                $img->resize($width, $height, function ($c) {
                    $c->aspectRatio();
                });
            }

            $img->stream();

            Storage::disk('public')->put("$path/$name", (string)$img);

            return [true, "$path/$name"];
        } catch (\Exception $e) {
            return [false, ""];
        }
    }

    public function uploadFile(UploadedFile $file, string $path): array
    {
        $name = $this->generateRandomString() . "_" . time() . "." . $file->getClientOriginalExtension();

        try {
            Storage::disk('public')->putFileAs($path, $file, $name);
            return [true, $path . "/" . $name];
        } catch (\Exception $e) {
            return [false, ""];
        }
    }

    public function generateRandomString(int $n = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

}
