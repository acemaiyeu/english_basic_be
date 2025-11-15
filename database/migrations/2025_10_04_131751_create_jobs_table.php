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
        // Schema::create('jobs', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('queue')->index();
        //     $table->longText('payload');
        //     $table->unsignedTinyInteger('attempts');
        //     $table->unsignedInteger('reserved_at')->nullable();
        //     $table->unsignedInteger('available_at');
        //     $table->unsignedInteger('created_at');
        // });
        // database/migrations/..._create_quizzes_table.php
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            // ... các trường khác
        });

        // database/migrations/..._create_questions_table.php
        // Schema::create('questions', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
        //     $table->text('content');
        //     $table->json('options'); // Lưu các đáp án dạng JSON
        //     $table->string('correct_answer'); // Đáp án đúng
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
