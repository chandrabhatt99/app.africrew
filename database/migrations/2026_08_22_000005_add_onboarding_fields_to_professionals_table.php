<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('full_name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('username')->nullable()->after('last_name');
            $table->string('cover_photo')->nullable()->after('profile_photo');
            $table->json('gallery_photos')->nullable()->after('cover_photo');
            $table->json('education')->nullable()->after('skills');
            $table->json('experience_records')->nullable()->after('education');
            $table->string('booking_policy')->default('instant')->after('availability');
            $table->json('availability_dates')->nullable()->after('booking_policy');
            $table->decimal('one_day_rate', 12, 2)->nullable()->after('hourly_rate');
            $table->decimal('two_day_rate', 12, 2)->nullable()->after('one_day_rate');
            $table->decimal('rehearsal_rate', 12, 2)->nullable()->after('two_day_rate');
            $table->unsignedInteger('multi_day_discount')->default(0)->after('rehearsal_rate');
            $table->json('payment_details')->nullable()->after('government_id');
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'username', 'cover_photo', 'gallery_photos',
                'education', 'experience_records', 'booking_policy', 'availability_dates',
                'one_day_rate', 'two_day_rate', 'rehearsal_rate', 'multi_day_discount', 'payment_details'
            ]);
        });
    }
};
