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
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffing_request_id')->nullable()->constrained('staffing_requests')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('transaction_type')->default('CustomerPayBillOnline'); // CustomerPayBillOnline, CustomerBuyGoodsOnline, B2C, C2B
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('checkout_request_id')->nullable()->unique();
            $table->string('mpesa_receipt_number')->nullable()->index();
            $table->string('phone_number', 25);
            $table->decimal('amount', 10, 2);
            $table->string('account_reference')->nullable();
            $table->string('transaction_desc')->nullable();
            $table->string('status')->default('pending')->index(); // pending, completed, failed, cancelled
            $table->integer('result_code')->nullable();
            $table->text('result_desc')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->json('raw_callback_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
