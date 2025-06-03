<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->decimal('score', 5, 2);
            $table->text('comments')->nullable();
            $table->enum('status', ['pending', 'graded', 'appealed', 'final'])->default('pending');
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            // Index pour améliorer les performances des requêtes
            $table->unique(['exam_id', 'student_id']);
            $table->index(['exam_id', 'status']);
            $table->index(['student_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_grades');
    }
}; 