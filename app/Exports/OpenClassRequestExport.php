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
 * Export class for OpenClassRequest data to Excel format
 */
class OpenClassRequestExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $query;
    protected $records;

    /**
     * Constructor
     * 
     * @param Builder|null $query Database query for filtered export
     * @param Collection|null $records Collection of specific records for bulk export
     */
    public function __construct(Builder $query = null, Collection $records = null)
    {
        $this->query = $query;
        $this->records = $records;
    }

    /**
     * Get the collection of data to export
     * 
     * @return Collection
     */
    public function collection()
    {
        if ($this->records) {
            // For bulk export of selected records, ensure relationships are loaded
            return $this->records->load(['subject', 'student.user', 'semester', 'students']);
        }

        if ($this->query) {
            return $this->query->with(['subject', 'student.user', 'semester', 'students'])->get();
        }

        return OpenClassRequest::with(['subject', 'student.user', 'semester', 'students'])->get();
    }

    /**
     * Define the column headings for the Excel export
     * 
     * @return array
     */
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

    /**
     * Map each record to the export format
     * 
     * @param OpenClassRequest $openClassRequest
     * @return array
     */
    public function map($openClassRequest): array
    {
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
        ];

        // Safely get student count
        $studentCount = 0;
        try {
            $studentCount = $openClassRequest->students_count ?? $openClassRequest->students()->count();
        } catch (\Exception $e) {
            // If counting fails, default to 0
            $studentCount = 0;
        }

        return [
            $openClassRequest->id,
            $openClassRequest->subject->name ?? 'N/A',
            $openClassRequest->subject->code ?? 'N/A',
            $openClassRequest->student->user->name ?? 'N/A',
            $openClassRequest->student->student_code ?? 'N/A',
            $openClassRequest->semester->name ?? 'N/A',
            $statusLabels[$openClassRequest->status] ?? $openClassRequest->status,
            $studentCount,
            $openClassRequest->note ?? '',
            $openClassRequest->admin_note ?? '',
            $openClassRequest->created_at ? $openClassRequest->created_at->format('d/m/Y H:i') : '',
            $openClassRequest->updated_at ? $openClassRequest->updated_at->format('d/m/Y H:i') : '',
        ];
    }

    /**
     * Apply styles to the worksheet
     * 
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}