<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::table('media', function (Blueprint $table) {
        // Add category column, defaulting to 'Campus' if none is provided
        $table->string('category')->default('Campus')->after('name');
    });
}

public function down(): void
{
    Schema::table('media', function (Blueprint $table) {
        $table->dropColumn('category');
    });
}

    /**
     * Reverse the migrations.
     */

};
