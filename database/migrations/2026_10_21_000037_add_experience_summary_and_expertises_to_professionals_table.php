<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            if (!Schema::hasColumn('professionals', 'experience_summary')) {
                $table->text('experience_summary')->nullable()->after('about');
            }
            if (!Schema::hasColumn('professionals', 'expertises')) {
                $table->json('expertises')->nullable()->after('skills');
            }
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            if (Schema::hasColumn('professionals', 'experience_summary')) {
                $table->dropColumn('experience_summary');
            }
            if (Schema::hasColumn('professionals', 'expertises')) {
                $table->dropColumn('expertises');
            }
        });
    }
};
