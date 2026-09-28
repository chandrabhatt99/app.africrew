<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('min_rate', 10, 2)->default(0)->after('description');
            $table->decimal('max_rate', 10, 2)->default(0)->after('min_rate');
        });

        Schema::table('professionals', function (Blueprint $table) {
            $table->json('social_links')->nullable()->after('preferred_locations');
            $table->date('interview_date')->nullable()->after('status');
            $table->string('interview_time')->nullable()->after('interview_date');
            $table->string('interviewer_name')->nullable()->after('interview_time');
            $table->text('interview_notes')->nullable()->after('interviewer_name');
            $table->string('interview_status')->default('pending')->after('interview_notes');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['min_rate', 'max_rate']);
        });

        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn([
                'social_links',
                'interview_date',
                'interview_time',
                'interviewer_name',
                'interview_notes',
                'interview_status'
            ]);
        });
    }
};
