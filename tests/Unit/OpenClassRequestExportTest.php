<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exports\OpenClassRequestExport;
use App\Exports\SingleOpenClassRequestExport;
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

    public function test_export_methods_exist(): void
    {
        $export = new OpenClassRequestExport();
        $this->assertTrue(method_exists($export, 'sheets'));
    }

    public function test_required_classes_can_be_autoloaded(): void
    {
        $this->assertTrue(interface_exists(\Maatwebsite\Excel\Concerns\FromArray::class));
        $this->assertTrue(interface_exists(\Maatwebsite\Excel\Concerns\WithMultipleSheets::class));
        $this->assertTrue(interface_exists(\Maatwebsite\Excel\Concerns\ShouldAutoSize::class));
        $this->assertTrue(interface_exists(\Maatwebsite\Excel\Concerns\WithStyles::class));
    }
}