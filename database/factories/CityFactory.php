<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->city();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'lat' => $this->faker->latitude(10, 18),
            'lng' => $this->faker->longitude(-12, -1),
        ];
    }
}
