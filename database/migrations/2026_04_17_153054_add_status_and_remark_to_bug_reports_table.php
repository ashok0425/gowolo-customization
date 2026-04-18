<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bug_reports', function (Blueprint $table) {
            // in_review (default) | duplicated | rejected | approved
            $table->string('status', 20)->default('in_review')->after('is_read');
            $table->text('remark')->nullable()->after('status');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('remark');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('bug_reports', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'remark', 'reviewed_by', 'reviewed_at']);
        });
    }
};
