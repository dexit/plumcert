<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_GB');
        $company = Company::first();
        $admin = User::where('role', 'admin')->first();

        $types = ['residential', 'landlord', 'commercial'];

        for ($i = 0; $i < 20; $i++) {
            Customer::create([
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'title' => $faker->randomElement(['Mr', 'Mrs', 'Ms', 'Dr']),
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'email' => $faker->unique()->safeEmail(),
                'tel' => $faker->phoneNumber(),
                'type' => $faker->randomElement($types),
                'address' => $faker->streetAddress(),
                'postcode' => $faker->postcode(),
            ]);
        }
    }
}
