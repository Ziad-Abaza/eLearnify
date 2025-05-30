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
        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('video_id')->primary();
            $table->foreignUuid('course_id')->constrained('courses', 'course_id')->cascadeOnDelete();
            $table->string('title', 200);
            $table->integer('duration')->nullable(); // Assuming duration in seconds or minutes
            $table->string('s3_url', 500)->nullable();
            $table->integer('order_in_course')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
