<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The spatie/laravel-activitylog package expects a batch_uuid column on the
 * activity_log table. If the table was created on an older schema (or the
 * package migration was skipped), inserts fail with "Unknown column
 * 'batch_uuid' in 'field list'". This migration adds it if missing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activity_log')) {
            return;
        }

        if (!Schema::hasColumn('activity_log', 'batch_uuid')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->uuid('batch_uuid')->nullable()->after('properties');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('activity_log', 'batch_uuid')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->dropColumn('batch_uuid');
            });
        }
    }
};
