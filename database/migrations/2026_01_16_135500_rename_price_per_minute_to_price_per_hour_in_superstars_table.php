<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `superstars` CHANGE `price_per_minute` `price_per_hour` DECIMAL(10,2) NOT NULL DEFAULT 0.00");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `superstars` CHANGE `price_per_hour` `price_per_minute` DECIMAL(10,2) NOT NULL DEFAULT 0.00");
    }
};
