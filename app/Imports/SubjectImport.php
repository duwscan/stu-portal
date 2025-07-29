<?php

namespace App\Imports;

use App\Models\ProgramSubject;
use App\Models\Subject;
use App\Models\TrainingProgram;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class SubjectImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
//        dd($collection[2]);
        $prerequisites = [];
        try {
            DB::beginTransaction();
            for($i = 2, $count = $collection->count(); $i < $count; $i++) {
                $row = $collection[$i];
                if($row[1] === null || $row[2] === null) {
                    continue; // Skip rows with missing subject code or name
                }
                $subject = Subject::firstOrCreate([
                    'code' => $row[1],
                ], [
                    'name' => $row[2],
                    'credits' =>  $row[6] ?? 0,
                    'is_active' => true,
                ]);
                $program = TrainingProgram::where('degree_type', $row[4])->first();
                if($program) {
                    $programSubject = ProgramSubject::firstOrCreate([
                        'training_program_id' =>  $program->id,
                        'subject_id' => $subject->id,
                    ], ['is_active' => true]);
                } else {
                    $program = TrainingProgram::firstOrCreate([
                        'code' => 'LC',
                    ], [
                        'name' => 'Lựa chọn',
                        'degree_type' => 'Lựa chọn',
                    ]);
                    $programSubject = ProgramSubject::firstOrCreate([
                        'training_program_id' =>  $program->id,
                        'subject_id' => $subject->id,
                    ], [
                        'is_active' => true,
                        'is_required' => !($row[3] == 'Lựa chọn'),
                    ]);
                }

                $prerequisites[] = [
                    'name' => $row[2],
                    'program_subject_id' => $programSubject->id,
                    'prerequisite_subject_name' => $row[13] != '' ? $this->splitBySemicolon($row[13]) : [],
                    'subject_corequisites_name' => $row[12] != '' ? $this->splitBySemicolon($row[12]) : [],
                ];
            }

            DB::commit();
        } catch (\Exception $e) {
            // Handle any exceptions that occur during processing
            dd($e->getMessage());
            DB::rollBack();
        }
//        dd($prerequisites);
        foreach ($prerequisites as $prerequisite) {
            $programSubject = ProgramSubject::find($prerequisite['program_subject_id']);
            if (!$programSubject) {
                continue; // Skip if program subject not found
            }

            $prerequisiteSubject = Subject::whereIn('name', $prerequisite['prerequisite_subject_name'])->pluck('id');
            $prerequisiteProgramSubject = ProgramSubject::whereIn('subject_id', $prerequisiteSubject)->pluck('id');
            // Xử lý các môn học tiên quyết
            $programSubject->prerequisites()->sync($prerequisiteProgramSubject);

            $coreprerequisiteSubject = Subject::whereIn('name', $prerequisite['subject_corequisites_name'])->pluck('id');
            $coreprerequisiteProgramSubject = ProgramSubject::whereIn('subject_id', $coreprerequisiteSubject)->pluck('id');
            // Xử lý các môn học tiên quyết
            $programSubject->corequisites()->sync($coreprerequisiteProgramSubject);
        }
    }

    public function splitBySemicolon(string $input, bool $removeEmpty = true): array
    {
        $parts = array_map('trim', explode(';', $input));

        if ($removeEmpty) {
            $parts = array_filter($parts, fn ($v) => $v !== '');
            // Nếu muốn reset key liên tục:
            $parts = array_values($parts);
        }

        return $parts;
    }
}
