<?php

namespace Database\Factories;

use App\Models\Testimonial;
use App\Services\UploaderService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Testimonial::class;

    public function definition(): array
    {
        $imageUrl = 'https://picsum.photos/300/300';
        $tempPath = storage_path('app/tmp/' . uniqid('testimonial_', true) . '.jpg');

        file_put_contents($tempPath, file_get_contents($imageUrl));

        $uploadedFile = new UploadedFile(
            $tempPath,
            basename($tempPath),
            mime_content_type($tempPath),
            null,
            true
        );

        $uploadFileService = new UploaderService();
        $folderName = env("APP_ENV") === "local" ? "public/testimonials" : "testimonials";

        [$isUploaded, $uploadedImage] = $uploadFileService->uploadImage($uploadedFile, $folderName);
        unlink($tempPath);

        return [
            'name' => $this->faker->name,
            'body' => $this->faker->paragraph(2),
            'rate' => $this->faker->numberBetween(3, 5),
            'image' => $uploadedImage,
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
