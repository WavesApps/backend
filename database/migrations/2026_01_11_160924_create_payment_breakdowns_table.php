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
        Schema::create('payment_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->decimal('superstar_amount', 10, 2);
            $table->decimal('system_amount', 10, 2);
            $table->decimal('superstar_percentage', 5, 2)->default(80.00);
            $table->decimal('system_percentage', 5, 2)->default(20.00);
            $table->timestamps();
            
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_breakdowns');
    }
};
