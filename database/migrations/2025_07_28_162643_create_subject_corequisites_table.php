<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subject_corequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_subject_id')->constrained('program_subjects')->onDelete('cascade');
            $table->foreignId('corequisite_id')->constrained('program_subjects')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_corequisites');
    }
};
