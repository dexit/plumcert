<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('cert_no')->unique();
            $table->string('type');
            $table->foreignId('job_id')->nullable()->constrained('service_jobs')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('boiler_id')->nullable()->constrained('boilers')->nullOnDelete();
            $table->foreignId('issued_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('issued_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->json('form_data')->nullable();
            $table->boolean('signed_by_engineer')->default(false);
            $table->boolean('signed_by_customer')->default(false);
            $table->string('signature_engineer_path')->nullable();
            $table->string('signature_customer_path')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('cert_no');
            $table->index('job_id');
            $table->index('customer_id');
            $table->index('property_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
