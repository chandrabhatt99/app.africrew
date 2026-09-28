<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staffing_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('company_name')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('event_name');
            $table->string('category');
            $table->unsignedInteger('staff_count');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location');
            $table->text('requirements')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->string('attachment')->nullable();
            $table->enum('status', [
                'new', 'under_review', 'staff_matching',
                'shortlisted', 'assigned', 'completed', 'cancelled'
            ])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staffing_requests');
    }
};
