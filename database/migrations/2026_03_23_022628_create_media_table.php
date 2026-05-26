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
    Schema::create('media', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Original file name
        $table->string('file_path'); // Path in storage
        $table->string('mime_type')->nullable(); // e.g., image/jpeg
        $table->unsignedBigInteger('size')->nullable(); // File size in bytes
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
