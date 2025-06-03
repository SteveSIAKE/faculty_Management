<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['midterm', 'final', 'quiz', 'assignment', 'other']);
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('date');
            $table->integer('duration'); // en minutes
            $table->decimal('total_points', 5, 2);
            $table->decimal('passing_score', 5, 2);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->string('room')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();

            // Index pour améliorer les performances des requêtes
            $table->index(['course_id', 'date']);
            $table->index(['teacher_id', 'date']);
            $table->index('status');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exams');
    }
}; 