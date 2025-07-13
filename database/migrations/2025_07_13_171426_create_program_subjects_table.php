<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('semester')->default(1); // Học kỳ
            $table->boolean('is_required')->default(true); // Môn bắt buộc hay tự chọn
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Một môn học chỉ được thêm một lần vào một chương trình
            $table->unique(['training_program_id', 'subject_id']);
        });

        // Bảng quan hệ môn học tiên quyết
        Schema::create('prerequisite_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('prerequisite_id')->constrained('program_subjects')->onDelete('cascade');
            $table->timestamps();

            // Một môn học tiên quyết chỉ được thêm một lần cho một môn học trong chương trình
            $table->unique(['program_subject_id', 'prerequisite_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prerequisite_subjects');
        Schema::dropIfExists('program_subjects');
    }
};
