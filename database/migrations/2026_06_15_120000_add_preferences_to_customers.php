<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('preferred_channel', ['email', 'sms', 'whatsapp'])->default('email')->after('notes');
            $table->boolean('marketing_opt_in')->default(false)->after('preferred_channel');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('gas_safe_registration')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['preferred_channel', 'marketing_opt_in']);
        });
    }
};
