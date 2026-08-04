<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->cascadeOnDelete();
            $table->foreignId('boiler_id')->nullable()->constrained('boilers')->cascadeOnDelete();
            $table->enum('type', ['annual_cert', 'service', 'invoice', 'custom'])->default('annual_cert');
            $table->dateTime('due_at');
            $table->dateTime('sent_at')->nullable();
            $table->enum('channel', ['email', 'sms', 'whatsapp'])->default('email');
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('due_at');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
