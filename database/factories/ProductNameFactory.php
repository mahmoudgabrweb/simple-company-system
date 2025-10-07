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
class ProductNameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'sector_name_ar' => $this->faker->randomElement(['قطاع المحرك', 'قطاع الكهرباء']),
            'sector_name_en' => $this->faker->randomElement(['Engine Sector', 'Electrical Sector']),
            'filter_name_ar' => $this->faker->randomElement(['فلتر عالي الجودة', 'فلتر قياسي']),
            'filter_name_en' => $this->faker->randomElement(['High-Quality Filter', 'Standard Filter']),
            'min_client_order_count' => 1,
            'max_client_order_count' => 5,
            'is_returnable' => $this->faker->boolean(),
            'has_international_shipping' => $this->faker->boolean(),
            'has_default_images' => $this->faker->boolean(),
            'is_active' => 1,
        ];
    }
}
