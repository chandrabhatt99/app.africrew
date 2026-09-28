<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('password');
            $table->string('profile_photo')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Kenya');
            $table->json('languages')->nullable();
            $table->string('category');
            $table->text('skills')->nullable();
            $table->unsignedInteger('experience_years')->default(0);
            $table->text('about')->nullable();
            $table->decimal('hourly_rate', 12, 2)->nullable();
            $table->decimal('half_day_rate', 12, 2)->nullable();
            $table->decimal('full_day_rate', 12, 2)->nullable();
            $table->string('availability')->default('available');
            $table->json('preferred_locations')->nullable();
            $table->string('resume')->nullable();
            $table->string('government_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professionals');
    }
};
