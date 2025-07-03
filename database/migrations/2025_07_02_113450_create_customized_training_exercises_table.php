<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customized_training_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained('subscription_training_plans')->onDelete('cascade');
            $table->integer('dayNumber');
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->integer('setNumber')->nullable();
            $table->integer('resp')->nullable();
            $table->decimal('weightKg', 8, 2)->nullable();
            $table->integer('duration')->nullable();
            $table->integer('reset_duration')->nullable();
            $table->text('notes')->nullable();
            $table->integer('order_in_day')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customized_training_exercises');
    }
};
