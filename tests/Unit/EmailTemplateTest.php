<?php

namespace Tests\Unit;

use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_render_substitutes_variables_in_subject_and_body(): void
    {
        $template = EmailTemplate::create([
            'key'       => 'demo',
            'name'      => 'Demo',
            'subject'   => 'Hi {{ customer_name }}, your {{ appliance }} is due {{ due_date }}',
            'body_html' => '<p>Dear {{ customer_name }}</p><p>{{ appliance }}</p>',
            'active'    => true,
        ]);

        $out = $template->render([
            'customer_name' => 'Jane Smith',
            'appliance'     => 'Worcester Bosch 4000',
            'due_date'      => '01 Jul 2026',
        ]);

        $this->assertSame('Hi Jane Smith, your Worcester Bosch 4000 is due 01 Jul 2026', $out['subject']);
        $this->assertStringContainsString('Dear Jane Smith', $out['body']);
        $this->assertStringContainsString('Worcester Bosch 4000', $out['body']);
    }

    public function test_missing_variables_render_as_empty_string(): void
    {
        $template = EmailTemplate::create([
            'key'       => 'demo2',
            'name'      => 'Demo2',
            'subject'   => 'Hello {{ unknown_var }}!',
            'body_html' => 'x',
            'active'    => true,
        ]);

        $this->assertSame('Hello !', $template->render([])['subject']);
    }

    public function test_find_by_key_ignores_inactive_templates(): void
    {
        EmailTemplate::create(['key' => 'on', 'name' => 'On', 'subject' => 's', 'body_html' => 'b', 'active' => true]);
        EmailTemplate::create(['key' => 'off', 'name' => 'Off', 'subject' => 's', 'body_html' => 'b', 'active' => false]);

        $this->assertNotNull(EmailTemplate::findByKey('on'));
        $this->assertNull(EmailTemplate::findByKey('off'));
        $this->assertNull(EmailTemplate::findByKey('missing'));
    }
}
