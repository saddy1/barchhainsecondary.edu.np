<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Change ENUM('college','school') → varchar(100) so any org slug is accepted
        DB::statement("ALTER TABLE `students` MODIFY `organization` VARCHAR(100) NOT NULL DEFAULT 'college'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `students` MODIFY `organization` ENUM('college','school') NOT NULL DEFAULT 'college'");
    }
};
