<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['PCS', 'Pieces'], ['SAK', 'Sak'], ['DUS', 'Dus'], ['PAIL', 'Pail'],
            ['BATANG', 'Batang'], ['METER', 'Meter'], ['LITER', 'Liter'], ['KG', 'Kilogram'],
            ['LEMBAR', 'Lembar'], ['ROLL', 'Roll'], ['SET', 'Set'], ['BOX', 'Box'],
            ['M3', 'Meter Kubik'], ['UNIT', 'Unit'],
        ];

        foreach ($units as [$code, $name]) {
            Unit::firstOrCreate(['code' => $code], ['name' => $name]);
        }
    }
}
