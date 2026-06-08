<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('student_count');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->foreignId('instructor_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('department_id')
                ->constrained('departments')
                ->onDelete('cascade');
            $table->foreignId('classroom_id')
                ->nullable()
                ->constrained('classrooms')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
