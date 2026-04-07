<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customization_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');

            // Who uploaded: 'user', 'technician', 'admin'
            $table->string('uploaded_by_type');
            $table->unsignedBigInteger('uploaded_by_id');

            // File category: logo, icon, background, document, attachment
            $table->string('file_category')->default('attachment');

            $table->string('original_name');
            $table->string('extension');
            $table->unsignedBigInteger('size_bytes')->nullable();

            // Storage: local path (during transition) or bunny path
            $table->string('local_path')->nullable();
            $table->string('bunny_path')->nullable();
            $table->boolean('bunny_synced')->default(false);

            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('customization_requests')->onDelete('cascade');
            $table->index('request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customization_files');
    }
};
