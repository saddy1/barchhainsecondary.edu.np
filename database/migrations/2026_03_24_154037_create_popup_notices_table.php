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
    Schema::create('popup_notices', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('image_path'); // The visual flyer
        $table->string('link_url')->nullable(); // Optional: Drive link or PDF link
        $table->boolean('is_active')->default(true);
        $table->integer('order')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('popup_notices');
    }
};
