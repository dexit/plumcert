<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vat_number')->nullable()->unique();
            $table->string('gas_safe_registration')->nullable();
            $table->json('bank_details')->nullable();
            $table->text('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('gas_safe_registration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
