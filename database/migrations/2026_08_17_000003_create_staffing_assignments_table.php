<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staffing_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffing_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('professional_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['assigned', 'accepted', 'declined', 'completed', 'cancelled'])->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['staffing_request_id', 'professional_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staffing_assignments');
    }
};
