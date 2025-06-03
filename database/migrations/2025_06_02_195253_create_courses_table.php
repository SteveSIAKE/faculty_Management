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
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->integer('credits');
            $table->string('semester');
            $table->integer('max_students')->default(30);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['name', 'status']);
            $table->index(['teacher_id', 'department_id']);
            $table->index('academic_year_id');
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
