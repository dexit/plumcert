<?php

namespace Tests\Feature;

use App\Models\Boiler;
use App\Models\Customer;
use App\Models\Property;
use App\Models\Reminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderGenerationTest extends TestCase
{
    use RefreshDatabase;

    private function boilerDueIn(int $days): Boiler
    {
        $customer = Customer::create(['first_name' => 'Reminder', 'last_name' => 'Test']);
        $property = Property::create(['customer_id' => $customer->id, 'address' => '2 Reminder Way']);

        return Boiler::create([
            'property_id'      => $property->id,
            'make'             => 'Vaillant',
            'model'            => 'ecoTEC',
            'next_service_due' => now()->addDays($days),
        ]);
    }

    public function test_generates_staggered_reminders_for_upcoming_service(): void
    {
        // due in 10 days: the 30-day lead window has passed, but 7-day (fires in
        // 3 days) and 0-day (fires in 10 days) are both still in the future
        $this->boilerDueIn(10);

        $this->artisan('reminders:generate')->assertSuccessful();

        $leads = Reminder::where('type', 'service')->pluck('lead_days')->sort()->values()->all();
        $this->assertContains(7, $leads);
        $this->assertContains(0, $leads);
        $this->assertNotContains(30, $leads, 'The 30-day lead is in the past and must not be backfilled.');
    }

    public function test_is_idempotent_across_runs(): void
    {
        $this->boilerDueIn(20);

        $this->artisan('reminders:generate')->assertSuccessful();
        $first = Reminder::count();

        $this->artisan('reminders:generate')->assertSuccessful();
        $this->assertSame($first, Reminder::count(), 'Re-running must not duplicate reminders.');
    }

    public function test_overdue_boiler_gets_overdue_reminder(): void
    {
        $this->boilerDueIn(-10); // overdue

        $this->artisan('reminders:generate')->assertSuccessful();

        $overdue = Reminder::where('type', 'service')->where('template_key', 'service_overdue')->first();
        $this->assertNotNull($overdue, 'Overdue boilers should still get an overdue reminder.');
    }
}
