<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Finding;
use App\Models\Job;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class FindingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_GB');
        $engineer = User::where('role', 'engineer')->first();

        if (!$engineer) {
            return;
        }

        $jobs = Job::all();

        if ($jobs->isEmpty()) {
            return;
        }

        $severities = ['ID', 'AR', 'NCS'];

        for ($i = 0; $i < 5; $i++) {
            Finding::create([
                'job_id' => $jobs->random()->id,
                'submitted_by_user_id' => $engineer->id,
                'title' => $faker->sentence(),
                'description' => $faker->paragraph(),
                'severity' => $faker->randomElement($severities),
                'status' => 'pending',
                'featured' => false,
            ]);
        }
    }
}
