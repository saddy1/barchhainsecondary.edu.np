<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'designation_id' => 'unsignedBigInteger',
                'employment_type_id' => 'unsignedBigInteger',
                'work_assigned_id' => 'unsignedBigInteger',
                'hajiri_department_id' => 'unsignedBigInteger',
                'device_id' => 'string',
                'province' => 'string',
                'district' => 'string',
                'municipal' => 'string',
                'status' => 'integer',
                'sort' => 'integer',
            ] as $column => $type) {
                if (! Schema::hasColumn('users', $column)) {
                    $definition = $table->{$type}($column)->nullable();
                    if ($column === 'status') {
                        $definition->default(1);
                    }
                    if ($column === 'sort') {
                        $definition->default(0);
                    }
                }
            }
        });

        Schema::create('hajiri_departments', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('alias')->nullable();
            $table->integer('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('designation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label', 50);
            $table->string('alias', 50)->nullable();
            $table->integer('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('employment_types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('work_assigneds', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('attendacelogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->dateTime('at');
            $table->timestamps();
            $table->unique(['user_id', 'at']);
        });

        Schema::create('holiday', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label', 100);
            $table->date('date');
            $table->string('color', 10)->default('#ff0000');
            $table->timestamps();
        });

        Schema::create('type_of_leaves', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('dsa')->default(0);
            $table->timestamps();
        });

        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name', 100);
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('type_of_leaves');
        Schema::dropIfExists('holiday');
        Schema::dropIfExists('attendacelogs');
        Schema::dropIfExists('work_assigneds');
        Schema::dropIfExists('employment_types');
        Schema::dropIfExists('designation');
        Schema::dropIfExists('hajiri_departments');
    }
};
