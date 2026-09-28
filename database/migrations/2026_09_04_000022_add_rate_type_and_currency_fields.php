<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            if (!Schema::hasColumn('professionals', 'rate_type')) {
                $table->string('rate_type')->default('fixed')->after('hourly_rate');
            }
            if (!Schema::hasColumn('professionals', 'currency')) {
                $table->string('currency')->default('USD')->after('rate_type');
            }
        });

        Schema::table('staffing_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('staffing_requests', 'rate_type')) {
                $table->string('rate_type')->default('fixed')->after('budget');
            }
            if (!Schema::hasColumn('staffing_requests', 'currency')) {
                $table->string('currency')->default('USD')->after('rate_type');
            }
            if (!Schema::hasColumn('staffing_requests', 'dates')) {
                $table->json('dates')->nullable()->after('event_date');
            }
            if (!Schema::hasColumn('staffing_requests', 'shift_duration')) {
                $table->string('shift_duration')->default('full_day')->after('end_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['rate_type', 'currency']);
        });

        Schema::table('staffing_requests', function (Blueprint $table) {
            $table->dropColumn(['rate_type', 'currency', 'dates', 'shift_duration']);
        });
    }
};
