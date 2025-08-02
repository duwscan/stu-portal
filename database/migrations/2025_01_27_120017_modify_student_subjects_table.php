<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_subjects', function (Blueprint $table) {
            // Xóa foreign key cũ
            // // $table->dropForeign(['subject_id']);
            // $table->dropColumn('subject_id');

            // Thêm foreign key mới đến program_subjects
            $table->foreignId('program_subject_id')
                ->constrained('program_subjects')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_subjects', function (Blueprint $table) {
            // Rollback: xóa foreign key mới
            $table->dropForeign(['program_subject_id']);
            $table->dropColumn('program_subject_id');

            // Thêm lại foreign key cũ
            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();
        });
    }
};
