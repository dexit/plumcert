<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            // Category-specific structured data captured by the pre-built job
            // forms (installation, maintenance, emergency, heating, plumbing,
            // custom, other). Keyed by category under this JSON column.
            $table->json('form_data')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropColumn('form_data');
        });
    }
};
