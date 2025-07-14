<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->decimal('grade', 3, 2)->nullable()->comment('Điểm theo thang 4.0');
            $table->enum('status', ['passed', 'failed'])->nullable()->comment('Trạng thái: Qua/Trượt');
            $table->timestamps();

            // Một sinh viên chỉ có một bản ghi điểm cho mỗi môn học
            $table->unique(['student_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subjects');
    }
};