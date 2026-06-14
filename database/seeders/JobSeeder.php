<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Boiler;
use App\Models\Certificate;
use App\Models\Customer;
use App\Models\InspectionItem;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Property;
use App\Models\Quote;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskGenerator;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_GB');
        $engineer = User::where('role', 'engineer')->first() ?? User::first();
        $admin = User::where('role', 'admin')->first() ?? User::first();

        $customers = Customer::with(['properties.boilers'])->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found — run CustomerSeeder + PropertySeeder + BoilerSeeder first.');
            return;
        }

        $jobTypes = ['gas_service', 'boiler_repair', 'cp12_inspection', 'installation', 'emergency_callout', 'annual_service'];
        $statuses = ['completed', 'completed', 'completed', 'in_progress', 'scheduled', 'cancelled'];

        $certNum = 1000;
        $invoiceNum = 2000;
        $quoteNum = 3000;

        foreach ($customers->take(15) as $customer) {
            $property = $customer->properties->first();
            $boiler   = $property?->boilers->first();

            $numJobs = $faker->numberBetween(1, 4);

            for ($j = 0; $j < $numJobs; $j++) {
                $type        = $faker->randomElement($jobTypes);
                $status      = $faker->randomElement($statuses);
                $scheduledAt = Carbon::now()->subDays($faker->numberBetween(0, 365));
                $completedAt = $status === 'completed' ? $scheduledAt->copy()->addHours($faker->numberBetween(1, 6)) : null;

                $job = Job::create([
                    'customer_id'         => $customer->id,
                    'property_id'         => $property?->id,
                    'assigned_to_user_id' => $engineer?->id,
                    'type'                => $type,
                    'title'               => self::jobTitle($type, $boiler),
                    'description'         => $faker->sentence(10),
                    'status'              => $status,
                    'scheduled_at'        => $scheduledAt,
                    'completed_at'        => $completedAt,
                    'notes'               => $faker->optional(0.4)->sentence(),
                ]);

                // Observer is suppressed in seeder — generate tasks manually
                app(TaskGenerator::class)->generate($job, 'job', $type, $scheduledAt);

                if ($status === 'completed') {
                    Task::where('taskable_type', Job::class)->where('taskable_id', $job->id)->update([
                        'status'       => 'done',
                        'completed_at' => $completedAt,
                    ]);
                }

                // Inspection items for service/inspection/annual jobs
                if ($status === 'completed' && in_array($type, ['gas_service', 'cp12_inspection', 'annual_service', 'boiler_repair'])) {
                    $this->createInspectionItems($job, $boiler, $property, $faker);
                }

                // Certificate for completed CP12 / gas service jobs
                if ($status === 'completed' && in_array($type, ['cp12_inspection', 'gas_service', 'annual_service'])) {
                    $certType = $customer->type === 'landlord' ? 'cp12_landlord' : 'cp12_homeowner';
                    $certNum++;
                    $cert = Certificate::create([
                        'job_id'             => $job->id,
                        'customer_id'        => $customer->id,
                        'property_id'        => $property?->id,
                        'boiler_id'          => $boiler?->id,
                        'issued_by_user_id'  => $engineer?->id ?? $admin?->id,
                        'certificate_number' => 'PC-' . $certNum,
                        'type'               => $certType,
                        'issued_at'          => $completedAt,
                        'signed_by_engineer' => true,
                        'signed_by_customer' => $faker->boolean(70),
                        'form_data'          => [
                            'client' => [
                                'name'  => trim($customer->first_name . ' ' . $customer->last_name),
                                'tel'   => $customer->tel ?? '',
                                'email' => $customer->email ?? '',
                            ],
                            'jobAddress' => [
                                'line1'    => $property?->address ?? '',
                                'postcode' => $property?->postcode ?? '',
                                'city'     => $property?->town ?? '',
                            ],
                            'appliances' => [[
                                'make'         => $boiler?->make ?? 'Worcester Bosch',
                                'model'        => $boiler?->model ?? 'ecoTEC Plus',
                                'serial'       => $boiler?->serial ?? $faker->bothify('##?##??#'),
                                'location'     => 'Kitchen',
                                'flue_type'    => 'Room Sealed',
                                'op_pressure'  => '1.2',
                                'gas_rate'     => '0.75',
                                'result'       => 'pass',
                                'safety_device' => 'Operational',
                            ]],
                            'safetyChecks' => [
                                'smoke_alarm_present', 'co_alarm_present', 'adequate_ventilation',
                            ],
                            'defects' => [],
                        ],
                    ]);

                    // Link inspection items to the cert
                    InspectionItem::where('job_id', $job->id)->update(['certificate_id' => $cert->id]);

                    // Observer is suppressed during seeding — schedule the recurring
                    // follow-up visit directly so the calendar shows future work.
                    app(\App\Services\RecurringJobScheduler::class)->scheduleFor($cert);
                }

                // Invoice for completed jobs
                if ($status === 'completed') {
                    $subtotal = $faker->randomFloat(2, 80, 600);
                    $vat      = round($subtotal * 0.20, 2);
                    $total    = $subtotal + $vat;
                    $paid     = $faker->boolean(70);
                    $invoiceNum++;

                    Invoice::create([
                        'customer_id' => $customer->id,
                        'job_id'      => $job->id,
                        'invoice_no'  => 'INV-' . $invoiceNum,
                        'line_items'     => [
                            ['description' => self::jobTitle($type, $boiler), 'qty' => 1, 'unit_price' => $subtotal, 'total' => $subtotal],
                        ],
                        'subtotal'  => $subtotal,
                        'vat'       => $vat,
                        'total'     => $total,
                        'paid_amount' => $paid ? $total : 0,
                        'due_date'  => $completedAt?->copy()->addDays(30)->toDateString(),
                        'paid_at'   => $paid ? $completedAt?->copy()->addDays($faker->numberBetween(1, 20)) : null,
                    ]);
                }

                // Quote for boiler_repair or installation — some accepted, some pending
                if (in_array($type, ['boiler_repair', 'installation'])) {
                    $subtotal = $faker->randomFloat(2, 300, 3500);
                    $vat      = round($subtotal * 0.20, 2);
                    $total    = $subtotal + $vat;
                    $quoteStatus = $faker->randomElement(['sent', 'accepted', 'rejected', 'draft']);
                    $quoteNum++;

                    Quote::create([
                        'customer_id' => $customer->id,
                        'job_id'      => $job->id,
                        'quote_no'    => 'QUO-' . $quoteNum,
                        'line_items'  => [
                            ['description' => 'Parts & Labour — ' . self::jobTitle($type, $boiler), 'qty' => 1, 'unit_price' => $subtotal, 'total' => $subtotal],
                        ],
                        'subtotal'    => $subtotal,
                        'vat'         => $vat,
                        'total'       => $total,
                        'status'      => $quoteStatus,
                        'valid_until' => now()->addDays($faker->numberBetween(14, 60))->toDateString(),
                        'notes'       => $faker->optional(0.5)->sentence(),
                    ]);
                }
            }
        }

        // Ensure a few upcoming/scheduled jobs for dashboard realism
        $sampleCustomers = $customers->take(5);
        foreach ($sampleCustomers as $customer) {
            $property = $customer->properties->first();
            $boiler   = $property?->boilers->first();
            Job::create([
                'customer_id'         => $customer->id,
                'property_id'         => $property?->id,
                'assigned_to_user_id' => $engineer?->id,
                'type'                => 'annual_service',
                'title'               => 'Annual Boiler Service — ' . trim($customer->first_name . ' ' . $customer->last_name),
                'status'              => 'scheduled',
                'scheduled_at'        => Carbon::now()->addDays($faker->numberBetween(1, 30)),
                'notes'               => 'Customer prefers morning appointment.',
            ]);
        }
    }

    private function createInspectionItems(Job $job, ?Boiler $boiler, ?Property $property, $faker): void
    {
        $result = $faker->randomElement(['pass', 'pass', 'pass', 'at_risk', 'fail']);

        // Gas boiler inspection
        InspectionItem::create([
            'job_id'      => $job->id,
            'property_id' => $property?->id,
            'category'    => 'gas_boiler',
            'location'    => 'Kitchen',
            'make'        => $boiler?->make ?? 'Worcester Bosch',
            'model'       => $boiler?->model ?? 'ecoTEC Plus',
            'serial'      => $boiler?->serial ?? $faker->bothify('##?##??#'),
            'gc_number'   => $boiler?->gc_number ?? $faker->bothify('GC-####-##'),
            'result'      => $result,
            'notes'       => $result === 'pass' ? 'Boiler serviced, all checks passed.' : 'Combustion analysis out of range — advised remedial work.',
            'data'        => [
                'flue_type'           => 'Room Sealed',
                'op_pressure_bar'     => $faker->randomFloat(1, 0.8, 1.5),
                'gas_rate_m3h'        => $faker->randomFloat(2, 0.5, 1.2),
                'heat_input_kw'       => $faker->randomFloat(1, 18, 30),
                'co_reading_ppm'      => $faker->numberBetween(0, 50),
                'co2_percent'         => $faker->randomFloat(1, 8.0, 10.5),
                'visual_condition'    => 'Good',
                'burner_pressure_mbar' => $faker->randomFloat(1, 12, 16),
                'heat_exchanger'      => 'Clean',
                'pilot_flame'         => 'N/A',
                'ignition_sequence'   => 'Normal',
                'condensate_drain'    => 'Clear',
                'expansion_vessel'    => $faker->randomElement(['OK', 'Pressurised correctly']),
                'pressure_relief_valve' => 'Operational',
                'system_pressure_bar' => $faker->randomFloat(1, 1.0, 1.5),
                'radiator_balance'    => 'Checked',
            ],
        ]);

        // CO alarm check
        InspectionItem::create([
            'job_id'      => $job->id,
            'property_id' => $property?->id,
            'category'    => 'co_alarm',
            'location'    => 'Hallway',
            'result'      => 'pass',
            'notes'       => 'CO alarm tested and working. Battery replaced.',
            'data'        => [
                'alarm_present' => true,
                'alarm_type'    => 'Kidde 9CO5',
                'battery_ok'    => true,
                'test_result'   => 'pass',
                'expiry_date'   => now()->addYears(7)->toDateString(),
            ],
        ]);

        // Smoke alarm
        InspectionItem::create([
            'job_id'      => $job->id,
            'property_id' => $property?->id,
            'category'    => 'smoke_alarm',
            'location'    => 'Landing',
            'result'      => 'pass',
            'notes'       => 'Smoke alarm tested and working.',
            'data'        => [
                'alarm_present' => true,
                'alarm_type'    => 'Ei Electronics 140',
                'battery_ok'    => true,
                'test_result'   => 'pass',
            ],
        ]);
    }

    private static function jobTitle(string $type, ?Boiler $boiler): string
    {
        $make = $boiler?->make ?? 'Boiler';
        $model = $boiler?->model ?? '';
        return match ($type) {
            'gas_service'       => "Gas Service — {$make} {$model}",
            'boiler_repair'     => "Boiler Repair — {$make} {$model}",
            'cp12_inspection'   => "CP12 Gas Safety Inspection",
            'installation'      => "Boiler Installation — {$make} {$model}",
            'emergency_callout' => "Emergency Call-Out — No Hot Water",
            'annual_service'    => "Annual Boiler Service — {$make} {$model}",
            default             => ucwords(str_replace('_', ' ', $type)),
        };
    }
}
