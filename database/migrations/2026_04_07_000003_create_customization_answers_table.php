<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customization_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('question_key');   // e.g. "question_1", "requirement_1"
            $table->text('question_text')->nullable();
            $table->text('answer')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('customization_requests')->onDelete('cascade');
            $table->index(['request_id', 'question_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customization_answers');
    }
};
