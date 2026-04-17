<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');            // 'new_request', 'new_chat', 'status_change'
            $table->string('title');
            $table->text('body');
            $table->string('icon')->default('fas fa-bell');
            $table->string('action_url')->nullable();
            $table->string('action_label')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_avatar')->nullable();
            $table->string('ref_number')->nullable();

            // Who should see this notification
            $table->unsignedBigInteger('notifiable_id')->nullable();  // portal_user id (null = all staff)
            $table->string('notifiable_type', 191)->default('staff');      // 'staff' or 'user'

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_notifications');
    }
};
