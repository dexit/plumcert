<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateRecurrenceTest extends TestCase
{
    use RefreshDatabase;

    private function customerWithProperty(): array
    {
        $customer = Customer::create(['first_name' => 'Test', 'last_name' => 'Owner']);
        $property = Property::create(['customer_id' => $customer->id, 'address' => '1 Test Road']);

        return [$customer, $property];
    }

    public function test_issuing_cp12_schedules_recurring_followup_job(): void
    {
        [$customer, $property] = $this->customerWithProperty();

        Certificate::create([
            'customer_id'        => $customer->id,
            'property_id'        => $property->id,
            'certificate_number' => 'CP12-1',
            'type'               => 'cp12_landlord',
            'issued_at'          => now(),
        ]);

        $job = Job::where('is_recurring', true)->first();

        $this->assertNotNull($job, 'A recurring follow-up job should be created.');
        $this->assertSame('annual_service', $job->type);
        $this->assertSame('scheduled', $job->status);
        // default cp12 cycle = 12 months
        $this->assertEqualsWithDelta(now()->addMonths(12)->timestamp, $job->scheduled_at->timestamp, 60 * 60 * 24 * 2);
    }

    public function test_recurrence_months_override_is_respected(): void
    {
        [$customer, $property] = $this->customerWithProperty();

        Certificate::create([
            'customer_id'        => $customer->id,
            'property_id'        => $property->id,
            'certificate_number' => 'CP12-2',
            'type'               => 'cp12_landlord',
            'recurrence_months'  => 6,
            'issued_at'          => now(),
        ]);

        $job = Job::where('is_recurring', true)->firstOrFail();
        $this->assertEqualsWithDelta(now()->addMonths(6)->timestamp, $job->scheduled_at->timestamp, 60 * 60 * 24 * 2);
    }

    public function test_non_recurring_type_creates_no_followup(): void
    {
        [$customer, $property] = $this->customerWithProperty();

        Certificate::create([
            'customer_id'        => $customer->id,
            'property_id'        => $property->id,
            'certificate_number' => 'WN-1',
            'type'               => 'warning_notice', // configured to 0 months
            'issued_at'          => now(),
        ]);

        $this->assertSame(0, Job::where('is_recurring', true)->count());
    }

    public function test_zero_override_disables_recurrence(): void
    {
        [$customer, $property] = $this->customerWithProperty();

        Certificate::create([
            'customer_id'        => $customer->id,
            'property_id'        => $property->id,
            'certificate_number' => 'CP12-3',
            'type'               => 'cp12_homeowner',
            'recurrence_months'  => 0,
            'issued_at'          => now(),
        ]);

        $this->assertSame(0, Job::where('is_recurring', true)->count());
    }

    public function test_followup_is_idempotent(): void
    {
        [$customer, $property] = $this->customerWithProperty();

        $cert = Certificate::create([
            'customer_id'        => $customer->id,
            'property_id'        => $property->id,
            'certificate_number' => 'CP12-4',
            'type'               => 'cp12_landlord',
            'issued_at'          => now(),
        ]);

        // touching / re-saving should not create a second follow-up
        $cert->touch();
        $cert->update(['issued_at' => now()]);

        $this->assertSame(1, Job::where('is_recurring', true)->count());
    }
}
