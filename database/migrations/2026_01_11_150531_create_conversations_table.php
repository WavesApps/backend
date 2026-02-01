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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_google_id');
            $table->unsignedBigInteger('superstar_id');
            $table->enum('status', ['active', 'ended', 'blocked'])->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_google_id')->references('id')->on('user_googles')->onDelete('cascade');
            $table->foreign('superstar_id')->references('id')->on('superstars')->onDelete('cascade');
            $table->unique(['user_google_id', 'superstar_id'], 'unique_chat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
