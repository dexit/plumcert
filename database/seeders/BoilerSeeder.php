<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Boiler;
use App\Models\Property;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BoilerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_GB');
        $properties = Property::all();

        $makes = ['Worcester Bosch', 'Vaillant', 'Baxi', 'Ideal', 'Viessmann', 'Potterton', 'Ariston'];
        $models = ['ecoTEC', 'ecoCOMP', 'Luna', 'Duo-TEC', 'Vitodens', 'Netaheat', 'Clas X'];

        foreach ($properties as $property) {
            $installDate = $faker->dateTimeBetween('-10 years', '-1 year');
            $lastServiceDate = clone $installDate;
            $lastServiceDate->modify('+' . $faker->numberBetween(1, 5) . ' years');

            Boiler::create([
                'property_id' => $property->id,
                'make' => $faker->randomElement($makes),
                'model' => $faker->randomElement($models),
                'serial' => $faker->bothify('##?##??#'),
                'gc_number' => $faker->bothify('GC-####-##'),
                'install_date' => $installDate,
                'last_service_date' => $lastServiceDate,
                'next_service_due' => $lastServiceDate->modify('+1 year'),
            ]);
        }
    }
}
