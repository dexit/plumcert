<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        User::create([
            'company_id' => $company->id,
            'name' => 'Admin',
            'email' => 'admin@plumcert.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => 'Dave Smith',
            'email' => 'dave@plumcert.local',
            'password' => Hash::make('password'),
            'role' => 'engineer',
            'gas_safe_id_card' => 'G123456',
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
    }
}
