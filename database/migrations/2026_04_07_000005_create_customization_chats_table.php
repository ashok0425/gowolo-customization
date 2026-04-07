<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customization_chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');

            // Polymorphic sender: 'user' (from SSO) or 'portal_user' (admin/tech)
            $table->string('sender_type'); // 'user' | 'portal_user'
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_name');

            $table->text('message')->nullable();

            // File attachment
            $table->string('local_path')->nullable();
            $table->string('bunny_path')->nullable();
            $table->boolean('bunny_synced')->default(false);
            $table->string('file_type')->nullable(); // image, pdf, video, document, unknown
            $table->string('original_filename')->nullable();

            // Read tracking
            $table->boolean('read_by_user')->default(false);
            $table->boolean('read_by_staff')->default(false);

            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('customization_requests')->onDelete('cascade');
            $table->index(['request_id', 'id']); // for polling: WHERE request_id=X AND id > last_id
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customization_chats');
    }
};
