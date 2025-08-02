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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Tiêu đề feedback
            $table->text('description'); // Mô tả chi tiết
            $table->string('tag')->nullable(); // Tag phân loại
            $table->string('status')->default('pending'); // Trạng thái: pending, reviewed, resolved
            $table->string('category')->nullable(); // Danh mục feedback
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};