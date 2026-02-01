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
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 100)->nullable()->unique()->after('name');
            });
        }
        
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone', 20)->nullable()->unique()->after('email');
            });
        }
        
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['superstar', 'admin'])->nullable()->after('password');
            });
        }
        
        if (!Schema::hasColumn('users', 'is_verified')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_verified')->default(false)->after('role');
            });
        }
        
        if (!Schema::hasColumn('users', 'is_blocked')) {
        Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_blocked')->default(false)->after('is_verified');
            });
        }
        
        if (!Schema::hasColumn('users', 'profile_image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('profile_image', 255)->nullable()->after('is_blocked');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'profile_image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('profile_image');
            });
        }
        
        if (Schema::hasColumn('users', 'is_blocked')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_blocked');
            });
        }
        
        if (Schema::hasColumn('users', 'is_verified')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_verified');
            });
        }
        
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
        
        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
        
        if (Schema::hasColumn('users', 'username')) {
        Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('username');
        });
        }
    }
};
