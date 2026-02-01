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
        Schema::table('user_googles', function (Blueprint $table) {
            $table->string('name', 100)->nullable()->after('email');
            $table->text('profile_image')->nullable()->after('username');
            $table->boolean('is_verified')->default(false)->after('profile_image');
        });

        // Make username nullable so seeders that don't provide it can insert
        DB::statement("ALTER TABLE `user_googles` MODIFY `username` VARCHAR(100) NULL;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_googles', function (Blueprint $table) {
            $table->dropColumn(['name', 'profile_image', 'is_verified']);
        });

        // Revert username to NOT NULL
        DB::statement("ALTER TABLE `user_googles` MODIFY `username` VARCHAR(100) NOT NULL;");
    }
};
