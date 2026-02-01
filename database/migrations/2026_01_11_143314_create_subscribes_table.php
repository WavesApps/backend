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
        Schema::create('subscribes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_google_id');
            $table->unsignedBigInteger('superstar_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_google_id')
                ->references('id')
                ->on('user_googles')
                ->onDelete('cascade');

            $table->foreign('superstar_id')
                ->references('id')
                ->on('superstars')
                ->onDelete('cascade');

            // Prevent duplicate subscriptions
            $table->unique(['user_google_id', 'superstar_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribes');
    }
};
