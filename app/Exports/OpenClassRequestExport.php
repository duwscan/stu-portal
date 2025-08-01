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

class OpenClassRequestExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
        if ($this->records) {
            // For bulk export of selected records
            return $this->records;
        }

        if ($this->query) {
            return $this->query->with(['subject', 'student.user', 'semester', 'students'])->get();
        }

        return OpenClassRequest::with(['subject', 'student.user', 'semester', 'students'])->get();
    }

    public function headings(): array
    {
        return [
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
    }

    public function map($openClassRequest): array
    {
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
        ];

        return [
            $openClassRequest->id,
            $openClassRequest->subject->name ?? '',
            $openClassRequest->subject->code ?? '',
            $openClassRequest->student->user->name ?? '',
            $openClassRequest->student->student_code ?? '',
            $openClassRequest->semester->name ?? '',
            $statusLabels[$openClassRequest->status] ?? $openClassRequest->status,
            $openClassRequest->students_count ?? $openClassRequest->students()->count(),
            $openClassRequest->note ?? '',
            $openClassRequest->admin_note ?? '',
            $openClassRequest->created_at ? $openClassRequest->created_at->format('d/m/Y H:i') : '',
            $openClassRequest->updated_at ? $openClassRequest->updated_at->format('d/m/Y H:i') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}