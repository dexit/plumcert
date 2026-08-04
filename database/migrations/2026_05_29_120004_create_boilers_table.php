<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boilers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('make');
            $table->string('model');
            $table->string('serial')->nullable();
            $table->string('gc_number')->nullable();
            $table->date('install_date')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('next_service_due')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('property_id');
            $table->index('gc_number');
            $table->index('next_service_due');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boilers');
    }
};
