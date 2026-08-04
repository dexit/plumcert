<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('status');
            $table->foreignId('recurs_from_certificate_id')->nullable()->after('is_recurring')
                ->constrained('certificates')->nullOnDelete();
            $table->index(['type', 'is_recurring', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropIndex(['type', 'is_recurring', 'scheduled_at']);
            $table->dropConstrainedForeignId('recurs_from_certificate_id');
            $table->dropColumn('is_recurring');
        });
    }
};
