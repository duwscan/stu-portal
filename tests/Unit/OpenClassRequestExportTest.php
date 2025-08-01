<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Exports\OpenClassRequestExport;
use App\Models\OpenClassRequest;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class OpenClassRequestExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_has_correct_headings()
    {
        $export = new OpenClassRequestExport();
        
        $expectedHeadings = [
            'ID',
            'Môn học',
            'Mã môn học',
            'Người tạo yêu cầu',
            'MSSV người tạo',
            'Học kỳ',
            'Trạng thái',
            'Số sinh viên tham gia',
            'Ghi chú của sinh viên',
            'Ghi chú của admin',
            'Ngày tạo',
            'Ngày cập nhật',
        ];
        
        $this->assertEquals($expectedHeadings, $export->headings());
    }

    public function test_export_can_handle_empty_collection()
    {
        $export = new OpenClassRequestExport(null, collect([]));
        
        $collection = $export->collection();
        
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertTrue($collection->isEmpty());
    }

    public function test_export_mapping_handles_null_relationships()
    {
        // Create a basic OpenClassRequest without relationships
        $request = new OpenClassRequest([
            'id' => 1,
            'status' => 'pending',
            'note' => 'Test note',
            'admin_note' => 'Admin note',
        ]);
        
        $export = new OpenClassRequestExport();
        $mapped = $export->map($request);
        
        // Verify basic structure
        $this->assertIsArray($mapped);
        $this->assertCount(12, $mapped);
        $this->assertEquals(1, $mapped[0]); // ID
        $this->assertEquals('Chờ xử lý', $mapped[6]); // Status mapping
        $this->assertEquals('Test note', $mapped[8]); // Note
        $this->assertEquals('Admin note', $mapped[9]); // Admin note
    }
}