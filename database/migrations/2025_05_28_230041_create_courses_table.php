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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->foreignUuid('instructor_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained('categories', 'category_id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
