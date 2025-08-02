<?php

namespace App\Imports;

use App\Models\ClassRoom;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ClassRoomImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $currentSemester = Semester::getCurrentSemester();

        if (!$currentSemester) {
            throw new \Exception('No current semester found. Please set the current semester before importing class rooms.');
        }
        // get all subjects
        $subjects = Subject::select('id', 'code')
            ->get()->toArray();
        $subjects = array_column($subjects, 'id', 'code');
        $shiftMap = [
            'Sáng' => 'morning',
            'Chiều' => 'afternoon',
        ];
        $dayOfWeekMap = [
            '2' => 'monday',
            '3' => 'tuesday',
            '4' => 'wednesday',
            '5' => 'thursday',
            '6' => 'friday',
            '7' => 'saturday',
        ];
        try{
            \DB::beginTransaction();
            for ($i = 3, $count = $collection->count(); $i < $count; $i++) {
                $row = $collection[$i];
                if ($row[0] === null || $row[1] === null) {
                    continue; // Skip rows with missing class code or name
                }
                $subjectCode = $row[2];
                $classCode = $row[6];
                $capacity = $row[9] ?? 0;
                $shift = $shiftMap[$row[10]] ?? null;
                $dayOfWeek = $dayOfWeekMap[$row[11]] ?? null;
                $subjectId = $subjects[$subjectCode] ?? null;
                $startTime = Helper::excelSerialToCarbon($row[16]);
                $endTime = Helper::excelSerialToCarbon($row[17]);
                if (!$subjectId) {
                    continue; // Skip if subject not found
                }

                ClassRoom::firstOrCreate([
                    'subject_id' =>  $subjectId,
                    'semester_id' => $currentSemester->id,
                    'code' => $classCode,
                    'user_id' => $this->getFirstAdmin()->id, // Assign the first admin as the teacher
                ], [
                    'capacity' => $capacity,
                    'is_open' =>  true,
                    'start_date' =>  $startTime,
                    'end_date' =>  $endTime,
                    'shift' => $shift,
                    'day_of_week' => $dayOfWeek,
                ]);
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e; // Re-throw the exception to handle it later
        }

    }

    //
    public function getFirstAdmin() {
        return \App\Models\User::role('admin')->first();
    }
}
