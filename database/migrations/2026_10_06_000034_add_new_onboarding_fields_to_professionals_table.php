<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->boolean('show_education_on_cv')->default(true)->after('education');
            $table->json('featured_skills')->nullable()->after('skills');
            $table->json('other_cities')->nullable()->after('preferred_locations');
            $table->json('blocked_dates')->nullable()->after('availability_dates');
            $table->json('assignment_types')->nullable()->after('availability');
            $table->integer('working_hours')->default(8)->after('full_day_rate');
            $table->string('rehearsal_option')->default('included')->after('rehearsal_rate');
            $table->boolean('has_multi_day_discount')->default(false)->after('rehearsal_option');
            $table->decimal('multi_day_discount_pct', 5, 2)->default(0)->after('has_multi_day_discount');
            $table->integer('multi_day_min_days')->default(2)->after('multi_day_discount_pct');
            $table->json('referees')->nullable()->after('experience_records');
            $table->boolean('is_public')->default(true)->after('status');
            $table->timestamp('submitted_for_review_at')->nullable()->after('is_public');
        });

        Schema::create('suggested_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained('professionals')->onDelete('cascade');
            $table->string('category');
            $table->string('skill_name');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suggested_skills');

        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn([
                'show_education_on_cv',
                'featured_skills',
                'other_cities',
                'blocked_dates',
                'assignment_types',
                'working_hours',
                'rehearsal_option',
                'has_multi_day_discount',
                'multi_day_discount_pct',
                'multi_day_min_days',
                'referees',
                'is_public',
                'submitted_for_review_at',
            ]);
        });
    }
};
