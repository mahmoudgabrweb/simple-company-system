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
class VehicleStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'is_active' => true,
        ];
    }
}
