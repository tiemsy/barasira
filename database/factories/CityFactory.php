<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        [$name, $lat, $lng] = $this->faker->randomElement([
            ['Bamako', 12.6392, -8.0029], ['Ségou', 13.4317, -6.2157],
            ['Sikasso', 11.3176, -5.6665], ['Kayes', 14.4469, -11.4447], ['Mopti', 14.4963, -4.1955],
        ]);

        return ['name' => $name, 'slug' => Str::slug($name).'-'.$this->faker->unique()->numerify('###'), 'lat' => $lat, 'lng' => $lng];
    }
}
