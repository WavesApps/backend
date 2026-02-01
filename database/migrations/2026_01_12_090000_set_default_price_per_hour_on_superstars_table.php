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
        // Ensure any NULL values are set to 0.00 before altering
        DB::statement("UPDATE `superstars` SET `price_per_minute` = 0.00 WHERE `price_per_minute` IS NULL;");

        // Modify column to have a default of 0.00
        DB::statement("ALTER TABLE `superstars` MODIFY `price_per_minute` DECIMAL(10,2) NOT NULL DEFAULT 0.00;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to having no default (keep NOT NULL)
        DB::statement("ALTER TABLE `superstars` MODIFY `price_per_minute` DECIMAL(10,2) NOT NULL;");
    }
};
