<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\TrainingProgram;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

        $password = bcrypt('password');
        DB::beginTransaction();
        try {
            for($i = 7, $count = $collection->count(); $i < $count; $i++) {
                $masv = $collection[$i][1];
                $user = User::create([
                    'email' => strtolower($masv).'@students.hou.edu.vn',
                    'name' => $collection[$i][2],
                    'password' => $password,
                ]);

                $user->assignRole('student');
                $class = $collection[$i][6];
                $program = TrainingProgram::firstOrCreate([
                    'code' => 'CN'
                ], [
                    'name' => 'Cử nhân',
                    'degree_type' => 'Cử nhân',
                ]);
                if(str_ends_with($class, 'KS')) {
                    $program = TrainingProgram::firstOrCreate([
                        'code' => 'KS'
                    ], [
                        'name' => 'Kỹ sư',
                        'degree_type' => 'Kỹ sư',
                    ]);
                }

                Student::create([
                    'user_id' => $user->id,
                    'student_code' => $masv,
                    'gender' => $collection[$i][4] === 'Nam' ? 'male' : 'female',
                    'address' => $collection[$i][5],
                    'class' => $collection[$i][6],
                    'faculty' => 'CNTT',
                    'birth_date' => Helper::excelSerialToCarbon($collection[$i][3]),
                    'training_program_id' => $program->id,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack(); // Có lỗi → rollback
            dd($e);
            throw $e;
        }


    }
}
