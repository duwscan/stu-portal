<?php

namespace App\Imports;

use App\Models\ProgramSubject;
use App\Models\Student;
use App\Models\StudentSubject;
use App\Models\Subject;
use DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class GradeImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        try {
            DB::beginTransaction();
            $headers = $collection[4];
            $subjects = [];
            foreach ($headers as $index => $header) {
                if ($index < 13) {
                    continue;
                }

                $code = $this->extractCourseCode($header);

                if ($code === "Ngày tổng hợp") {
                    continue; // Skip if the header is "Ngày tổng hợp"
                }

                $subjects[] = [
                    'code' => $this->extractCourseCode($header),
                    'index' => $index,
                ];
            }

            for ($int = 7; $count = $collection->count(), $int < $count; $int++) {
                $row = $collection[$int];
                $student = Student::where('student_code', $row[1])->first();
                if (!$student) {
                    continue; // Skip if student not found
                }
                $trainingProgram = $student->training_program_id;
                foreach ($subjects as $subject) {
                    $grade = $row[$subject['index']];
                    if ($grade === null || $grade === '') {
                        continue;
                    }
                    $subject = Subject::where('code', $subject['code'])->first();
                    if (!$subject) {
                        continue; // Skip if subject not found
                    }

                    $programSubject = ProgramSubject::where('training_program_id', $trainingProgram)
                        ->where('subject_id', $subject->id)->first();
                    if (!$programSubject) {
                        continue; // Skip if program subject not found
                    }

                    StudentSubject::create([
                        'student_id' => $student->id,
                        'program_subject_id' => $programSubject->id,
                        'grade' => $grade,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle any exceptions that occur during processing
            throw $e;
        }
    }

    public function extractCourseCode($input)
    {
        $parts = explode(' - ', $input);
        return isset($parts[0]) ? trim($parts[0]) : null;
    }

}
