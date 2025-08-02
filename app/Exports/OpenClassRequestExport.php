<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\OpenClassRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class OpenClassRequestExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        
        // Get all open class requests with their related data
        $requests = OpenClassRequest::with([
            'subject',
            'student.user',
            'semester',
            'students.user'
        ])->get();

        foreach ($requests as $request) {
            $sheets[] = new OpenClassRequestSheetExport($request);
        }

        return $sheets;
    }
}