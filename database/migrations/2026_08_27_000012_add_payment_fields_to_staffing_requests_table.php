<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staffing_requests', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status'); // unpaid, paid_to_admin, released_to_crew, refunded
            $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_status');
            $table->string('payment_method')->nullable()->after('payment_amount'); // mpesa, bank_transfer, card, admin_record
            $table->timestamp('paid_at')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('staffing_requests', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_amount', 'payment_method', 'paid_at']);
        });
    }
};
