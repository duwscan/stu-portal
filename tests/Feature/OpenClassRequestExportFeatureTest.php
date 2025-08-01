<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\OpenClassRequest;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Semester;
use App\Models\TrainingProgram;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OpenClassRequestExport;

class OpenClassRequestExportFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->createTestData();
    }

    private function createTestData()
    {
        // Create a training program
        $trainingProgram = TrainingProgram::create([
            'code' => 'CN',
            'name' => 'Cử nhân',
            'degree_type' => 'Cử nhân',
        ]);

        // Create a user and student
        $user = User::create([
            'name' => 'Test Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'student_code' => 'SV001',
            'gender' => 'male',
            'address' => 'Test Address',
            'class' => 'Test Class',
            'faculty' => 'CNTT',
            'birth_date' => '2000-01-01',
            'training_program_id' => $trainingProgram->id,
        ]);

        // Create a subject
        $subject = Subject::create([
            'code' => 'CS101',
            'name' => 'Introduction to Computer Science',
            'credits' => 3,
        ]);

        // Create a semester
        $semester = Semester::create([
            'name' => 'Học kỳ 1 - 2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2024-12-31',
            'is_current' => true,
        ]);

        // Create an open class request
        OpenClassRequest::create([
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'semester_id' => $semester->id,
            'status' => 'pending',
            'note' => 'Test request note',
            'admin_note' => 'Test admin note',
        ]);
    }

    public function test_export_downloads_excel_file()
    {
        Excel::fake();

        $export = new OpenClassRequestExport();
        
        Excel::download($export, 'test-export.xlsx');

        Excel::assertDownloaded('test-export.xlsx', function (OpenClassRequestExport $export) {
            return true;
        });
    }

    public function test_export_contains_expected_data()
    {
        $export = new OpenClassRequestExport();
        $collection = $export->collection();

        $this->assertNotEmpty($collection);
        $this->assertEquals(1, $collection->count());

        $first = $collection->first();
        $this->assertEquals('CS101', $first->subject->code);
        $this->assertEquals('SV001', $first->student->student_code);
        $this->assertEquals('pending', $first->status);
    }

    public function test_export_mapping_returns_correct_format()
    {
        $export = new OpenClassRequestExport();
        $collection = $export->collection();
        $request = $collection->first();

        $mapped = $export->map($request);

        $this->assertIsArray($mapped);
        $this->assertCount(12, $mapped);
        
        // Check some specific mappings
        $this->assertEquals('Introduction to Computer Science', $mapped[1]); // Subject name
        $this->assertEquals('CS101', $mapped[2]); // Subject code
        $this->assertEquals('Test Student', $mapped[3]); // Student name
        $this->assertEquals('SV001', $mapped[4]); // Student code
        $this->assertEquals('Chờ xử lý', $mapped[6]); // Status (translated)
    }
}