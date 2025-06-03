<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['start_date', 'end_date']);
            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
}; 