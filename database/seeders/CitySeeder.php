<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['Bamako', 12.6392, -8.0029],
            ['Ségou', 13.4317, -6.2157],
            ['Sikasso', 11.3176, -5.6665],
            ['Kita', 12.7333, -9.4833],
            ['Kayes', 14.4469, -11.4447],
            ['Mopti', 14.4963, -4.1955],
            ['Gao', 16.2667, -0.0500],
            ['Tombouctou', 16.7735, -3.0074],
        ];

        foreach ($cities as [$name, $lat, $lng]) {
            City::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'lat' => $lat,
                'lng' => $lng,
            ]);
        }
    }
}
