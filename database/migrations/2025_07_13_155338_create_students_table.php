<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_code')->unique(); // Mã sinh viên
            $table->date('birthday')->nullable(); // Ngày sinh
            $table->string('gender', 10)->nullable(); // Giới tính
            $table->string('phone')->nullable(); // Số điện thoại
            $table->string('class')->nullable(); // Lớp
            $table->string('faculty')->nullable(); // Khoa
            $table->string('address')->nullable(); // Địa chỉ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
