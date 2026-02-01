<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('superstarstories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('postedby_userid');
            $table->enum('file_type', ['image', 'video']);
            $table->string('url_path');
            $table->timestamp('timestap')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('postedby_userid')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('postedby_userid');
            $table->index('timestap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('superstarstories');
    }
};
