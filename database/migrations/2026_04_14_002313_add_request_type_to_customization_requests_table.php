<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customization_requests', function (Blueprint $table) {
            // customization | graphic_design | web_development | software_development | app_development
            $table->string('request_type', 50)->default('customization')->after('cuid');
        });
    }

    public function down(): void
    {
        Schema::table('customization_requests', function (Blueprint $table) {
            $table->dropColumn('request_type');
        });
    }
};
