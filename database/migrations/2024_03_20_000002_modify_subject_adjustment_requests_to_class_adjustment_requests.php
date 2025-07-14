<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Đổi tên bảng
        Schema::rename('subject_adjustment_requests', 'class_adjustment_requests');

        // Sửa lại cấu trúc bảng
        Schema::table('class_adjustment_requests', function (Blueprint $table) {
            // Xóa các cột cũ
            $table->dropForeign(['from_subject_id']);
            $table->dropForeign(['to_subject_id']);
            $table->dropColumn(['from_subject_id', 'to_subject_id']);

            // Thêm các cột mới
            $table->foreignId('from_class_id')->nullable()->constrained('class_rooms')->after('semester_id');
            $table->foreignId('to_class_id')->nullable()->constrained('class_rooms')->after('from_class_id');

            // Sửa lại enum type
            $table->dropColumn('type');
            $table->enum('type', ['cancel', 'change', 'register'])->after('to_class_id');
        });
    }

    public function down(): void
    {
        Schema::table('class_adjustment_requests', function (Blueprint $table) {
            // Xóa các cột mới
            $table->dropForeign(['from_class_id']);
            $table->dropForeign(['to_class_id']);
            $table->dropColumn(['from_class_id', 'to_class_id']);

            // Thêm lại các cột cũ
            $table->foreignId('from_subject_id')->nullable()->constrained('subjects')->after('semester_id');
            $table->foreignId('to_subject_id')->nullable()->constrained('subjects')->after('from_subject_id');

            // Sửa lại enum type
            $table->dropColumn('type');
            $table->enum('type', ['cancel', 'change'])->after('to_subject_id');
        });

        // Đổi tên bảng lại
        Schema::rename('class_adjustment_requests', 'subject_adjustment_requests');
    }
};
