<?php

namespace Database\Factories;

use App\Services\UploaderService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 *
 */
class SliderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $imageUrl = 'https://picsum.photos/1200/400';
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
        $folderName = env("APP_ENV") === "local" ? "public/sliders" : "sliders";
        list($isUploaded, $uploadedImage) = $uploadFileService->uploadImage($uploadedFile, $folderName);
        unlink($tempPath);

        return [
            "header_ar" => $this->faker->sentence(10),
            "header_en" => $this->faker->sentence(10),
            "title_ar" => $this->faker->sentence(7),
            "title_en" => $this->faker->sentence(7),
            "description_ar" => $this->faker->sentence(12),
            "description_en" => $this->faker->sentence(12),
            'image' => $uploadedImage,
            'link' => $this->faker->url,
            'is_active' => 1,
        ];
    }
}
