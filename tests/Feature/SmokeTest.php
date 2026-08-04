<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertSuccessful();
    }

    public function test_admin_login_redirects_guests(): void
    {
        $this->get('/admin')->assertRedirect();
    }
}
