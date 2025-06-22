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
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
            $table->text('Bio')->nullable();
            $table->integer('experience_years')->default(0);
            $table->integer('subscriber_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->decimal('subscription_price',6,2)->nullable();
            $table->decimal('rating_weight_subscribers', 5, 2)->default(0.30);
            $table->decimal('rating_weight_certificates', 5, 2)->default(0.30);
            $table->decimal('rating_weight_trainee_feedback', 5, 2)->default(0.40);
            $table->date('last_rating_calculated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
