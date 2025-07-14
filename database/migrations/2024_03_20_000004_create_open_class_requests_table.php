<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('open_class_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete(); // Người tạo yêu cầu
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('note')->nullable(); // Ghi chú của người tạo yêu cầu
            $table->text('admin_note')->nullable(); // Ghi chú của admin khi duyệt/từ chối
            $table->timestamps();
            $table->softDeletes();
        });

        // Bảng pivot để lưu danh sách sinh viên tham gia yêu cầu
        Schema::create('open_class_request_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_class_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['open_class_request_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_class_request_student');
        Schema::dropIfExists('open_class_requests');
    }
};
