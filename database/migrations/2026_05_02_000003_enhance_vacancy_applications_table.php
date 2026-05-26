<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacancy_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // Personal details
            $table->string('profile_photo')->nullable()->after('cv_path');
            $table->date('date_of_birth')->nullable()->after('profile_photo');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('father_name')->nullable()->after('gender');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('permanent_address')->nullable()->after('mother_name');
            $table->string('temporary_address')->nullable()->after('permanent_address');

            // Citizenship
            $table->string('citizenship_no')->nullable()->after('temporary_address');
            $table->string('citizen_front_path')->nullable()->after('citizenship_no');
            $table->string('citizen_back_path')->nullable()->after('citizen_front_path');

            // Signature
            $table->string('signature_path')->nullable()->after('citizen_back_path');
        });
    }

    public function down(): void
    {
        Schema::table('vacancy_applications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id', 'profile_photo', 'date_of_birth', 'gender',
                'father_name', 'mother_name', 'permanent_address', 'temporary_address',
                'citizenship_no', 'citizen_front_path', 'citizen_back_path', 'signature_path',
            ]);
        });
    }
};
