<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('org_type', 50);    // school | college | (any org slug)
            $table->string('member_type', 50); // student | staff | teacher
            $table->string('file_path', 255);  // relative to public/ e.g. erp/card/img/bg/...
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index(['org_type', 'member_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_backgrounds');
    }
};
