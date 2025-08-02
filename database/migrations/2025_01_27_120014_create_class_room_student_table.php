<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_room_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamps();

            // Đảm bảo một sinh viên không thể đăng ký cùng một lớp nhiều lần
            $table->unique(['class_room_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_room_student');
    }
};
