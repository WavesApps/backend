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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_google_id');
            $table->unsignedBigInteger('superstar_id');
            $table->decimal('total_amount', 10, 2);
            $table->unsignedBigInteger('chat_sessions_id')->nullable();
            $table->enum('payment_method', ['wallet', 'card', 'mobile_money']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_reference', 100)->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_google_id')->references('id')->on('user_googles');
            $table->foreign('superstar_id')->references('id')->on('superstars');
            // Note: chat_sessions_id foreign key will be added after chat_sessions table is created
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
