<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['notice', 'event', 'news'])->default('notice');
            $table->string('category')->nullable(); // e.g., Academic, Admission, Sports
            
            $table->longText('content'); // Rich Text (WordPress style)
            $table->text('excerpt')->nullable(); // Short summary for cards
            
            // Image Handling
            $table->enum('image_type', ['upload', 'link'])->default('upload');
            $table->string('featured_image')->nullable(); // Stores local path OR Drive link
            
            // Specific to Events
            $table->date('event_date')->nullable();
            $table->string('event_time')->nullable();
            $table->string('event_location')->nullable();
            
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};