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
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('review_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('movie_id');
            $table->unsignedTinyInteger('rating'); // Phù hợp cho thang điểm 1-10
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps(); // Bao gồm created_at và updated_at

            // Foreign key constraints
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('movie_id')->references('movie_id')->on('movies')->onDelete('cascade');

            // Unique constraint: a user can review a movie only once
            $table->unique(['user_id', 'movie_id'], 'unique_user_movie_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
