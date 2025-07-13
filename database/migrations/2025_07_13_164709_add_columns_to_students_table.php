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
        Schema::table('students', function (Blueprint $table) {
            // Đổi tên cột birthday thành birth_date
            $table->renameColumn('birthday', 'birth_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Đổi tên lại từ birth_date thành birthday
            $table->renameColumn('birth_date', 'birthday');
        });
    }
};
