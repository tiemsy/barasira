<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MunicipalityFactory extends Factory
{
    protected $model = Municipality::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(['Commune I', 'Commune II', 'Commune III', 'Commune IV', 'Commune V', 'Commune VI']);

        return ['city_id' => City::factory(), 'name' => $name, 'slug' => Str::slug($name).'-'.$this->faker->unique()->numerify('###')];
    }
}
