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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->index();
            }
            if (!Schema::hasColumn('users', 'facebook_id')) {
                $table->string('facebook_id')->nullable()->index();
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (!Schema::hasColumn('users', 'auth_provider')) {
                $table->string('auth_provider')->nullable();
            }
        });

        Schema::table('professionals', function (Blueprint $table) {
            if (!Schema::hasColumn('professionals', 'google_id')) {
                $table->string('google_id')->nullable()->index();
            }
            if (!Schema::hasColumn('professionals', 'facebook_id')) {
                $table->string('facebook_id')->nullable()->index();
            }
            if (!Schema::hasColumn('professionals', 'auth_provider')) {
                $table->string('auth_provider')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'facebook_id', 'avatar', 'auth_provider']);
        });

        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'facebook_id', 'auth_provider']);
        });
    }
};
