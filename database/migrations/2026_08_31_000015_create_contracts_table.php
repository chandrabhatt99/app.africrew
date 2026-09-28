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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffing_request_id')->constrained('staffing_requests')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // client
            $table->foreignId('professional_id')->nullable()->constrained('professionals')->onDelete('set null');
            $table->string('contract_number')->unique();
            $table->longText('terms');
            $table->timestamp('client_signed_at')->nullable();
            $table->string('client_signature')->nullable();
            $table->timestamp('crew_signed_at')->nullable();
            $table->string('crew_signature')->nullable();
            $table->enum('status', ['pending', 'client_signed', 'crew_signed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
