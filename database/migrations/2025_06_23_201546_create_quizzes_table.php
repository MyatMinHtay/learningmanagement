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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->default(1);
            $table->integer('total_marks')->default(0);
            $table->integer('total_questions')->default(0);
            $table->integer('total_time')->default(0)->nullable();
            $table->boolean('is_time_limited')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
