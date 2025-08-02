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
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã chương trình đào tạo
            $table->string('name'); // Tên chương trình
            $table->text('description')->nullable(); // Mô tả
            $table->integer('total_credits')->default(0); // Tổng số tín chỉ
            $table->integer('duration_years')->default(4); // Thời gian đào tạo (năm)
            $table->string('degree_type'); // Loại bằng cấp (Cử nhân, Kỹ sư, ...)
            $table->string('specialization')->nullable(); // Chuyên ngành
            $table->date('effective_date'); // Ngày bắt đầu hiệu lực
            $table->date('expiration_date')->nullable(); // Ngày hết hiệu lực
            $table->boolean('is_active')->default(true); // Trạng thái hoạt động
            $table->timestamps();
            $table->softDeletes(); // Thêm soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_programs');
    }
};
