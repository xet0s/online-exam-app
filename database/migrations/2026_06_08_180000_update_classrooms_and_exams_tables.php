<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('building')->nullable()->after('name');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('building');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
