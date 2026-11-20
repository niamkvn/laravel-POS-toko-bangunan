<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['employee_code' => 'EMP-0001', 'name' => 'Super Admin', 'email' => 'admin@example.com', 'role' => 'Super Admin'],
            ['employee_code' => 'EMP-0002', 'name' => 'Owner Toko', 'email' => 'owner@example.com', 'role' => 'Owner'],
            ['employee_code' => 'EMP-0003', 'name' => 'Budi Kasir', 'email' => 'cashier@example.com', 'role' => 'Kasir'],
            ['employee_code' => 'EMP-0004', 'name' => 'Warehouse Staff', 'email' => 'warehouse@example.com', 'role' => 'Gudang'],
            ['employee_code' => 'EMP-0005', 'name' => 'Manager Operasional', 'email' => 'manager@example.com', 'role' => 'Manager'],
            ['employee_code' => 'EMP-0006', 'name' => 'Purchasing Staff', 'email' => 'purchasing@example.com', 'role' => 'Purchasing'],
            ['employee_code' => 'EMP-0007', 'name' => 'Accounting Staff', 'email' => 'accounting@example.com', 'role' => 'Accounting'],
            ['employee_code' => 'EMP-0008', 'name' => 'Sales Staff', 'email' => 'sales@example.com', 'role' => 'Sales'],
            ['employee_code' => 'EMP-0009', 'name' => 'Driver Toko', 'email' => 'driver@example.com', 'role' => 'Driver'],
        ];

        foreach ($users as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'employee_code' => $u['employee_code'],
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$u['role']]);
        }
    }
}
