<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customization_requests', function (Blueprint $table) {
            $table->string('cuid', 26)->nullable()->after('id');
        });

        // Backfill existing rows with ULIDs (sortable, URL-safe, collision-resistant)
        DB::table('customization_requests')->whereNull('cuid')->orderBy('id')->lazyById(100)
            ->each(function ($row) {
                DB::table('customization_requests')
                    ->where('id', $row->id)
                    ->update(['cuid' => (string) Str::ulid()]);
            });

        // Lock it down: not-null + unique
        Schema::table('customization_requests', function (Blueprint $table) {
            $table->string('cuid', 26)->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('customization_requests', function (Blueprint $table) {
            $table->dropUnique(['cuid']);
            $table->dropColumn('cuid');
        });
    }
};
