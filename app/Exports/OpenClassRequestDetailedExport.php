<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\OpenClassRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Detailed export class for OpenClassRequest with participating students information
 */
class OpenClassRequestDetailedExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $query;
    protected $records;

    public function __construct(Builder $query = null, Collection $records = null)
    {
        $this->query = $query;
        $this->records = $records;
    }

    public function collection()
    {
        $requests = collect();
        
        if ($this->records) {
            $requests = $this->records->load(['subject', 'student.user', 'semester', 'students.user']);
        } elseif ($this->query) {
            $requests = $this->query->with(['subject', 'student.user', 'semester', 'students.user'])->get();
        } else {
            $requests = OpenClassRequest::with(['subject', 'student.user', 'semester', 'students.user'])->get();
        }

        // Flatten the data to include each participating student as a separate row
        $flattened = collect();
        
        foreach ($requests as $request) {
            if ($request->students->count() > 0) {
                foreach ($request->students as $student) {
                    $flattened->push((object) [
                        'request' => $request,
                        'participating_student' => $student,
                    ]);
                }
            } else {
                // If no students, still include the request
                $flattened->push((object) [
                    'request' => $request,
                    'participating_student' => null,
                ]);
            }
        }

        return $flattened;
    }

    public function headings(): array
    {
        return [
            'ID Yêu cầu',
            'Môn học',
            'Mã môn học',
            'Người tạo yêu cầu',
            'MSSV người tạo',
            'Học kỳ',
            'Trạng thái',
            'Tổng số SV tham gia',
            'MSSV tham gia',
            'Tên SV tham gia',
            'Email SV tham gia',
            'Lớp SV tham gia',
            'Ghi chú của sinh viên',
            'Ghi chú của admin',
            'Ngày tạo',
            'Ngày tham gia',
        ];
    }

    public function map($row): array
    {
        $request = $row->request;
        $participatingStudent = $row->participating_student;
        
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
        ];

        return [
            $request->id,
            $request->subject->name ?? 'N/A',
            $request->subject->code ?? 'N/A',
            $request->student->user->name ?? 'N/A',
            $request->student->student_code ?? 'N/A',
            $request->semester->name ?? 'N/A',
            $statusLabels[$request->status] ?? $request->status,
            $request->students()->count(),
            $participatingStudent ? $participatingStudent->student_code : '',
            $participatingStudent ? $participatingStudent->user->name : '',
            $participatingStudent ? $participatingStudent->user->email : '',
            $participatingStudent ? $participatingStudent->class : '',
            $request->note ?? '',
            $request->admin_note ?? '',
            $request->created_at ? $request->created_at->format('d/m/Y H:i') : '',
            $participatingStudent && $participatingStudent->pivot ? 
                $participatingStudent->pivot->created_at->format('d/m/Y H:i') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}