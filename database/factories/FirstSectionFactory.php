<?php

namespace Database\Factories;

use App\Services\UploaderService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FirstSection>
 */
class FirstSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageUrl = 'https://picsum.photos/300/300';
        $tempPath = storage_path('app/tmp/' . time() . '.jpg');
        file_put_contents($tempPath, file_get_contents($imageUrl));

        $uploadedFile = new UploadedFile(
            $tempPath,
            basename($tempPath),
            mime_content_type($tempPath),
            null,
            true // Mark as test mode (no real HTTP request)
        );
        $uploadFileService = new UploaderService();
        $folderName = env("APP_ENV") === "local" ? "public/first_sections" : "first_sections";
        list($isUploaded, $uploadedImage) = $uploadFileService->uploadImage($uploadedFile, $folderName);

        unlink($tempPath);

        return [
            'is_active' => 1,
            'image' => $uploadedImage,
            'position' => $this->faker->numberBetween(1, 10),
        ];
    }
}
