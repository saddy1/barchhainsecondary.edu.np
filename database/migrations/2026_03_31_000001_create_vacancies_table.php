<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('department')->nullable(); // e.g., Teaching, Admin, Support
            $table->string('type')->default('Full Time'); // Full Time, Part Time, Contract
            $table->date('deadline')->nullable();
            $table->string('document_path')->nullable(); // attached vacancy PDF
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
