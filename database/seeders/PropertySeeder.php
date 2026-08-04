<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Property;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_GB');

        foreach (Customer::all() as $customer) {
            $count = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $count; $i++) {
                Property::create([
                    'customer_id' => $customer->id,
                    'address' => $faker->streetAddress(),
                    'postcode' => $faker->postcode(),
                    'town' => $faker->city(),
                    'county' => $faker->county(),
                    'notes' => null,
                ]);
            }
        }
    }
}
