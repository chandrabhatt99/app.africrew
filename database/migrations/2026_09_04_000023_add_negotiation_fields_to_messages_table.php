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
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('proposal_id')->nullable()->constrained('proposals')->onDelete('cascade')->after('staffing_request_id');
            $table->decimal('negotiated_price', 10, 2)->nullable()->after('message');
            $table->string('negotiation_status')->nullable()->after('negotiated_price');
            $table->json('price_breakdown')->nullable()->after('negotiation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['proposal_id']);
            $table->dropColumn(['proposal_id', 'negotiated_price', 'negotiation_status', 'price_breakdown']);
        });
    }
};
