<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name', 191)->nullable()->index();
            $table->text('description');
            $table->string('subject_type', 191)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->index(['subject_type', 'subject_id'], 'subject_subject_type_subject_id_index');
            $table->string('event', 191)->nullable();
            $table->string('causer_type', 191)->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->index(['causer_type', 'causer_id'], 'causer_causer_type_causer_id_index');
            $table->json('attribute_changes')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }
};
