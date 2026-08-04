<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Per-certificate recurrence override (months). Null = use the
            // configured default for the certificate type.
            $table->unsignedSmallInteger('recurrence_months')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('recurrence_months');
        });
    }
};
