<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Sınav Haftası');
            $table->date('start_date');
            $table->date('end_date');
            // null = tüm fakülte için geçerli (admin/dekan tarafından tanımlanır)
            // dolu = sadece o bölüm için geçerli (bölüm başkanı tanımlayabilir)
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->onDelete('cascade');
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_periods');
    }
};
