<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->dateTime('date');
            $table->enum('type', ['absence', 'retard']);
            $table->enum('status', ['pending', 'justified', 'unjustified'])->default('pending');
            $table->text('justification')->nullable();
            $table->string('justification_file')->nullable();
            $table->foreignId('justified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('justified_at')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            // Index pour améliorer les performances des requêtes fréquentes
            $table->index(['student_id', 'date']);
            $table->index(['course_id', 'date']);
            $table->index(['type', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('absences');
    }
}; 