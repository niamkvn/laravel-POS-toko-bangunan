<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class DriverVehicleSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            ['DRV-001', 'Dedi Kurniawan', '081111222333', 'SIM-B1-001'],
            ['DRV-002', 'Joko Susilo', '085666777888', 'SIM-B1-002'],
            ['DRV-003', 'Rudi Hartono', '081234567890', 'SIM-B2-003'],
        ];

        foreach ($drivers as [$code, $name, $phone, $license]) {
            Driver::firstOrCreate(['driver_code' => $code], [
                'name' => $name, 'phone' => $phone, 'license_number' => $license, 'active' => true,
            ]);
        }

        $vehicles = [
            ['VHC-001', 'B 1234 ABC', 'Pickup', '1 Ton', 'DRV-001'],
            ['VHC-002', 'B 5678 DEF', 'L300', '1.5 Ton', 'DRV-002'],
            ['VHC-003', 'B 9012 GHI', 'Truck Engkel', '4 Ton', 'DRV-003'],
        ];

        foreach ($vehicles as [$code, $plate, $type, $capacity, $driverCode]) {
            Vehicle::firstOrCreate(['vehicle_code' => $code], [
                'plate_number' => $plate, 'vehicle_type' => $type, 'capacity' => $capacity,
                'driver_id' => Driver::where('driver_code', $driverCode)->first()?->id,
                'active' => true,
            ]);
        }
    }
}
