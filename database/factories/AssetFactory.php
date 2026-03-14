<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'serial_number' => fake()->unique()->regexify('[A-Z]{2}-[0-9]{4}'),
            'status' => fake()->randomElement(['active', 'inactive', 'maintenance']),
        ];
    }
}
