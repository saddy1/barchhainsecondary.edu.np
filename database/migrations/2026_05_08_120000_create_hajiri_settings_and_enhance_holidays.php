<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hajiri_settings', function (Blueprint $table) {
            $table->id();
            $table->time('office_start_time')->default('10:00:00');
            $table->time('office_end_time')->default('16:00:00');
            $table->unsignedSmallInteger('late_grace_minutes')->default(10);
            $table->unsignedSmallInteger('early_grace_minutes')->default(10);
            $table->json('weekend_days')->nullable();
            $table->timestamps();
        });

        Schema::table('holiday', function (Blueprint $table) {
            if (! Schema::hasColumn('holiday', 'alias')) {
                $table->string('alias', 100)->nullable()->after('label');
            }
            if (! Schema::hasColumn('holiday', 'status')) {
                $table->boolean('status')->default(true)->after('color');
            }
            if (! Schema::hasColumn('holiday', 'dsa')) {
                $table->boolean('dsa')->default(false)->after('status');
            }
        });

        DB::table('hajiri_settings')->insert([
            'office_start_time' => '10:00:00',
            'office_end_time' => '16:00:00',
            'late_grace_minutes' => 10,
            'early_grace_minutes' => 10,
            'weekend_days' => json_encode([0, 6]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('hajiri_settings');

        Schema::table('holiday', function (Blueprint $table) {
            foreach (['dsa', 'status', 'alias'] as $column) {
                if (Schema::hasColumn('holiday', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
