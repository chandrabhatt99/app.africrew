<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            if (!Schema::hasColumn('professionals', 'my_update')) {
                $table->string('my_update', 255)->nullable()->after('experience_summary');
            }
            if (!Schema::hasColumn('professionals', 'lets_connect')) {
                $table->string('lets_connect', 255)->nullable()->after('my_update');
            }
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            if (Schema::hasColumn('professionals', 'my_update')) {
                $table->dropColumn('my_update');
            }
            if (Schema::hasColumn('professionals', 'lets_connect')) {
                $table->dropColumn('lets_connect');
            }
        });
    }
};
