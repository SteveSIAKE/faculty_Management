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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'excused']);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index pour améliorer les performances des requêtes fréquentes
            $table->index(['student_id', 'date']);
            $table->index(['course_id', 'date']);
            $table->index(['status']);

            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['student_id', 'course_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendancess');
    }
};
