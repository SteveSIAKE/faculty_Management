<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('head_id')->nullable()->after('description')->constrained('users')->onDelete('set null');
            $table->index('head_id');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['head_id']);
            $table->dropColumn('head_id');
        });
    }
}; 