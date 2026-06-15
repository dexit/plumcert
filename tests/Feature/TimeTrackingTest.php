<?php
namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Job;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TimeTrackingTest extends TestCase
{
    use RefreshDatabase;

    private function makeJob(): Job
    {
        $customer = Customer::create(['first_name' => 'Time', 'last_name' => 'Test']);
        return Job::create([
            'customer_id' => $customer->id,
            'type'        => 'maintenance',
            'title'       => 'Test job',
            'status'      => 'in_progress',
        ]);
    }

    public function test_engineer_can_clock_in_and_out(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $job = $this->makeJob();

        $this->postJson("/api/v1/jobs/{$job->id}/clock-in")
            ->assertStatus(201)
            ->assertJsonFragment(['message' => 'Clocked in']);

        $this->assertDatabaseHas('time_entries', ['job_id' => $job->id, 'user_id' => $user->id]);

        $this->postJson("/api/v1/jobs/{$job->id}/clock-out")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Clocked out']);

        $entry = TimeEntry::first();
        $this->assertNotNull($entry->clocked_out_at);
        $this->assertNotNull($entry->minutes);
    }

    public function test_cannot_clock_in_twice(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $job = $this->makeJob();
        $this->postJson("/api/v1/jobs/{$job->id}/clock-in")->assertStatus(201);
        $this->postJson("/api/v1/jobs/{$job->id}/clock-in")->assertStatus(409);
    }

    public function test_clock_out_without_clock_in_returns_404(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $job = $this->makeJob();
        $this->postJson("/api/v1/jobs/{$job->id}/clock-out")->assertStatus(404);
    }
}
