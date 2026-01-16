<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\City;
use App\Models\Municipality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bamako = City::where('slug', 'bamako')->first();

        $communes = [
            'Commune I',
            'Commune II',
            'Commune III',
            'Commune IV',
            'Commune V',
            'Commune VI',
        ];

        foreach ($communes as $commune) {
            Municipality::create([
                'city_id' => $bamako->id,
                'name' => $commune,
                'slug' => Str::slug($commune),
            ]);
        }
    }
}
