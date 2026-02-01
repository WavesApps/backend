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
        Schema::create('superstar_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('media_type', ['image', 'video']);
            $table->enum('resource_type', ['upload', 'url']);
            $table->string('resource_url_path', 500);
            $table->boolean('is_pg')->default(false);
            $table->boolean('is_disturbing')->default(false);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('superstars')->onDelete('cascade');
            $table->index(['media_type', 'is_pg', 'is_disturbing']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('superstar_posts');
    }
};
