<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exports\ClassAdjustmentRequestExport;
use PHPUnit\Framework\TestCase;

class ClassAdjustmentRequestExportTest extends TestCase
{
    public function test_export_has_correct_headings(): void
    {
        $export = new ClassAdjustmentRequestExport();
        $headings = $export->headings();

        $expectedHeadings = [
            'Mã sinh viên',
            'Tên sinh viên', 
            'Học kỳ',
            'Từ lớp',
            'Môn học (Từ)',
            'Đến lớp', 
            'Môn học (Đến)',
            'Loại yêu cầu',
            'Trạng thái',
            'Lý do',
            'Ghi chú admin',
            'Ngày tạo',
        ];

        $this->assertEquals($expectedHeadings, $headings);
    }

    public function test_export_class_exists(): void
    {
        $this->assertTrue(class_exists(ClassAdjustmentRequestExport::class));
    }

    public function test_export_implements_required_interfaces(): void
    {
        $export = new ClassAdjustmentRequestExport();
        
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\FromCollection::class, $export);
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\WithHeadings::class, $export);
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\WithMapping::class, $export);
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\ShouldAutoSize::class, $export);
    }
}