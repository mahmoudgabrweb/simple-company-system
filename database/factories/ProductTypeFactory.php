<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductType>
 */
class ProductTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $types = [
            ['ar' => 'قطع غيار', 'en' => 'Spare Part'],
            ['ar' => 'منتج عادي', 'en' => 'Standard Product'],
        ];

        $type = $this->faker->randomElement($types);

        return [
            'name_ar' => $type['ar'],
            'name_en' => $type['en'],
            'is_active' => 1,
        ];
    }
}
