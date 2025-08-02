<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exports\OpenClassRequestExport;
use App\Exports\SingleOpenClassRequestExport;
use App\Models\OpenClassRequest;
use PHPUnit\Framework\TestCase;

class OpenClassRequestExportTest extends TestCase
{
    public function test_export_class_exists(): void
    {
        $this->assertTrue(class_exists(OpenClassRequestExport::class));
        $this->assertTrue(class_exists(SingleOpenClassRequestExport::class));
    }

    public function test_export_implements_required_interfaces(): void
    {
        $export = new OpenClassRequestExport();
        
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\WithMultipleSheets::class, $export);
    }

    public function test_single_export_implements_required_interfaces(): void
    {
        // Create a mock OpenClassRequest for testing
        $mockRequest = $this->createMock(OpenClassRequest::class);
        $mockRequest->method('load')->willReturnSelf();
        $mockRequest->subject = (object) ['name' => 'Test Subject', 'code' => 'TEST01', 'credits' => 3];
        $mockRequest->semester = (object) ['name' => 'Semester 1'];
        $mockRequest->student = (object) ['user' => (object) ['name' => 'Test Student'], 'student_code' => 'SV001'];
        $mockRequest->status = 'pending';
        $mockRequest->note = 'Test note';
        $mockRequest->admin_note = 'Admin note';
        $mockRequest->created_at = now();
        $mockRequest->students = collect();
        $mockRequest->student_id = 1;
        
        $export = new SingleOpenClassRequestExport($mockRequest);
        
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\FromArray::class, $export);
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\ShouldAutoSize::class, $export);
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\WithStyles::class, $export);
    }

    public function test_single_export_array_structure(): void
    {
        // Create a mock OpenClassRequest for testing
        $mockRequest = $this->createMock(OpenClassRequest::class);
        $mockRequest->method('load')->willReturnSelf();
        $mockRequest->subject = (object) ['name' => 'Test Subject', 'code' => 'TEST01', 'credits' => 3];
        $mockRequest->semester = (object) ['name' => 'Semester 1'];
        $mockRequest->student = (object) ['user' => (object) ['name' => 'Test Student'], 'student_code' => 'SV001'];
        $mockRequest->status = 'pending';
        $mockRequest->note = 'Test note';
        $mockRequest->admin_note = 'Admin note';
        $mockRequest->created_at = now();
        $mockRequest->students = collect();
        $mockRequest->student_id = 1;
        $mockRequest->method('getAttribute')->with('students')->willReturn(collect());
        
        $export = new SingleOpenClassRequestExport($mockRequest);
        $array = $export->array();
        
        // Check that the array starts with header information
        $this->assertEquals('THÔNG TIN YÊU CẦU MỞ LỚP', $array[0][0]);
        $this->assertEquals('Môn học:', $array[1][0]);
        $this->assertEquals('Test Subject', $array[1][1]);
        
        // Check student list header
        $this->assertContains(['DANH SÁCH SINH VIÊN THAM GIA'], $array);
        $this->assertContains([
            'STT',
            'Mã sinh viên',
            'Họ và tên',
            'Lớp',
            'Khoa',
            'Vai trò',
            'Ngày tham gia',
        ], $array);
    }
}