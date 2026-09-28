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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffing_request_id')->constrained('staffing_requests')->onDelete('cascade');
            $table->foreignId('professional_id')->constrained('professionals')->onDelete('cascade');
            $table->decimal('custom_quote_amount', 10, 2);
            $table->decimal('travel_fee', 10, 2)->default(0);
            $table->decimal('accommodation_fee', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'accepted', 'countered', 'rejected'])->default('pending');
            $table->decimal('counter_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
