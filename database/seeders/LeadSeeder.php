<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Lead;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_GB');
        $statuses = ['new', 'contacted', 'converted'];

        for ($i = 0; $i < 10; $i++) {
            Lead::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'postcode' => $faker->postcode(),
                'service_type' => $faker->randomElement(['Gas Safety Certificate', 'Boiler Service', 'Boiler Install']),
                'message' => $faker->sentence(),
                'status' => $faker->randomElement($statuses),
                'source' => $faker->randomElement(['website', 'phone', 'referral', 'social']),
            ]);
        }
    }
}
