<?php

namespace Database\Seeders;

use App\Models\TrainingProgram;
use Illuminate\Database\Seeder;

class TrainningProgramSeeder extends Seeder
{
    public function run(): void
    {
        $program = TrainingProgram::firstOrCreate([
            'code' => 'KS'
        ], [
            'name' => 'Kỹ sư',
            'degree_type' => 'Kỹ sư',
        ]);

        $program = TrainingProgram::firstOrCreate([
            'code' => 'CN'
        ], [
            'name' => 'Cử nhân',
            'degree_type' => 'Cử nhân',
        ]);

        $program = TrainingProgram::firstOrCreate([
            'code' => 'LC'
        ], [
            'name' => 'Lựa chọn',
            'degree_type' => 'Lựa chọn',
        ]);
    }
}
