<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Customizable email templates ──
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();      // service_reminder, service_overdue, annual_cert_renewal, certificate_delivery
            $table->string('name');
            $table->string('subject');
            $table->longText('body_html');
            $table->text('description')->nullable();
            $table->string('from_name')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // ── Fix reminders: add columns the model/command expect ──
        Schema::table('reminders', function (Blueprint $table) {
            $table->foreignId('certificate_id')->nullable()->after('boiler_id')
                ->constrained('certificates')->nullOnDelete();
            $table->string('title')->nullable()->after('type');
            $table->text('description')->nullable()->after('title');
            $table->unsignedInteger('lead_days')->nullable()->after('due_at'); // days before due that this reminder fires
            $table->string('template_key')->nullable()->after('lead_days');     // which email template to use
            $table->index(['type', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropIndex(['type', 'sent_at']);
            $table->dropColumn(['title', 'description', 'lead_days', 'template_key']);
        });

        Schema::dropIfExists('email_templates');
    }
};
