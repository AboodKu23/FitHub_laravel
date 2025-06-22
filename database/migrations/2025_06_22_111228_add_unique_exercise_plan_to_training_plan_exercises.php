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
        Schema::table('training_plan_exercises', function (Blueprint $table) {
            $table->unique(['training_plan_id', 'dayNumber', 'exercise_id'], 'unique_exercise_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('training_plan_exercises', function (Blueprint $table) {
            //
        });
    }
};
