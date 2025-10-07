<?php

namespace Database\Factories;

use App\Models\SecondSection;
use App\Models\VehicleMaker;
use App\Services\UploaderService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SecondSection>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $imageUrl = 'https://picsum.photos/300/300';
        $tempPath = storage_path('app/tmp/' . time() . '.jpg');
        file_put_contents($tempPath, file_get_contents($imageUrl));

        $uploadedFile = new UploadedFile(
            $tempPath,
            basename($tempPath),
            mime_content_type($tempPath),
            null,
            true
        );

        $uploadFileService = new UploaderService();
        $folderName = env("APP_ENV") === "local" ? "public/brands" : "brands";
        list($isUploaded, $uploadedImage) = $uploadFileService->uploadImage($uploadedFile, $folderName);
        unlink($tempPath);

        return [
            'image' => $uploadedImage,
            'position' => $this->faker->numberBetween(1, 10),
            'is_active' => 1,
        ];
    }
}
