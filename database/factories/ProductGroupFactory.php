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
class ProductGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name_ar' => $this->faker->unique()->randomElement([
                'مجموعة الفلاتر',
                'مجموعة المكابح',
                'مجموعة الإطارات',
                'مجموعة البطاريات',
                'مجموعة الزيوت'
            ]),
            'name_en' => $this->faker->unique()->randomElement([
                'Filter Group',
                'Brake Group',
                'Tire Group',
                'Battery Group',
                'Oil Group'
            ]),
            'is_active' => true,
        ];
    }
}
