<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Enrich customers as entity types ──
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('category', ['homeowner', 'landlord', 'commercial'])
                ->default('homeowner')->after('type');
            $table->string('business_type')->nullable()->after('category'); // estate_agent, letting_agent, property_management, other
            $table->string('contact_name')->nullable()->after('business_type'); // primary contact at a commercial entity
        });

        // map legacy type -> category
        DB::table('customers')->where('type', 'residential')->update(['category' => 'homeowner']);
        DB::table('customers')->where('type', 'landlord')->update(['category' => 'landlord']);
        DB::table('customers')->where('type', 'commercial')->update(['category' => 'commercial']);

        // ── Fix latent Job column mismatch (model expects title/description) ──
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->string('title')->nullable()->after('property_id');
            $table->text('description')->nullable()->after('title');
        });

        // ── Tasks / Todos (polymorphic: Job, Quote, Certificate) ──
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->morphs('taskable'); // taskable_type, taskable_id
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'done', 'skipped'])->default('pending');
            $table->dateTime('due_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_auto')->default(false);       // auto-generated from template
            $table->boolean('photo_required')->default(false); // needs a photo to complete
            $table->timestamps();

            $table->index('status');
            $table->index('assigned_to_user_id');
            $table->index('due_at');
        });

        // ── Task templates (auto-generate task lists per trigger) ──
        Schema::create('task_templates', function (Blueprint $table) {
            $table->id();
            $table->string('trigger');          // job, quote, certificate
            $table->string('applies_to')->nullable(); // optional job/cert type filter (null = all)
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('offset_days')->default(0); // due_at = anchor + offset_days
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('photo_required')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('trigger');
        });

        // ── Photos (polymorphic: Job, Task, InspectionItem, Certificate) ──
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->morphs('photoable');
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('path');
            $table->string('thumb_path')->nullable();
            $table->string('caption')->nullable();
            $table->enum('phase', ['start', 'during', 'finish', 'defect'])->default('during');
            $table->timestamps();

            $table->index('phase');
        });

        // ── Inspection items (advanced per-appliance logging) ──
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->nullable()->constrained('service_jobs')->cascadeOnDelete();
            $table->foreignId('certificate_id')->nullable()->constrained('certificates')->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->enum('category', [
                'gas_appliance', 'gas_boiler', 'heater', 'plumbing',
                'radiators', 'co_alarm', 'smoke_alarm',
            ]);
            $table->string('location')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('serial')->nullable();
            $table->string('gc_number')->nullable();
            $table->enum('result', ['pass', 'fail', 'at_risk', 'id', 'na'])->default('na');
            $table->json('data')->nullable();   // category-specific readings
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('job_id');
            $table->index('category');
            $table->index('result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('task_templates');
        Schema::dropIfExists('tasks');

        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['category', 'business_type', 'contact_name']);
        });
    }
};
