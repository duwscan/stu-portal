<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã môn học
            $table->string('name'); // Tên môn học
            $table->integer('credits')->default(1); // Số tín chỉ
            $table->text('description')->nullable(); // Mô tả môn học
            $table->boolean('is_active')->default(true); // Trạng thái hoạt động
            $table->timestamps();
            $table->softDeletes(); // Thêm soft delete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
