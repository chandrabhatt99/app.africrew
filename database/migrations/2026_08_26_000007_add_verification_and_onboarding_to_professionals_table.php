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
        Schema::table('professionals', function (Blueprint $table) {
            $table->string('full_name')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('category')->nullable()->change();
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('verification_token')->nullable()->after('email_verified_at');
            $table->boolean('is_onboarded')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['email_verified_at', 'verification_token', 'is_onboarded']);
        });
    }
};
