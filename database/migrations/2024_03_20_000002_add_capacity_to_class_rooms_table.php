<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->unsignedInteger('capacity')->default(40)->comment('Sức chứa tối đa của lớp');
            $table->boolean('is_open')->default(true)->comment('Trạng thái mở/đóng của lớp');
        });
    }

    public function down(): void
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->dropColumn(['capacity', 'is_open']);
        });
    }
};
