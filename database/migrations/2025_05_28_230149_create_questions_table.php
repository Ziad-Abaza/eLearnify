<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('question_id')->primary();
            $table->foreignUuid('video_id')->constrained('videos', 'video_id')->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['mcq', 'single_choice', 'true_false'])
                ->default('mcq');
            $table->integer('points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
