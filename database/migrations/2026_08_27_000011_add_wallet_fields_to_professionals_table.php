<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->decimal('wallet_pending', 10, 2)->default(0.00)->after('status');
            $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('wallet_pending');
            $table->decimal('wallet_withdrawn', 10, 2)->default(0.00)->after('wallet_balance');
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['wallet_pending', 'wallet_balance', 'wallet_withdrawn']);
        });
    }
};
