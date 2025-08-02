<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\OpenClassRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class OpenClassRequestSheetExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles
{
    private OpenClassRequest $request;

    public function __construct(OpenClassRequest $request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        // Return the students participating in this request
        return $this->request->students()->with('user')->get();
    }

    public function headings(): array
    {
        // First add request information headers
        $requestInfo = [
            ['THÔNG TIN YÊU CẦU MỞ LỚP'],
            ['Môn học:', $this->request->subject?->name ?? ''],
            ['Mã môn học:', $this->request->subject?->code ?? ''],
            ['Số tín chỉ:', $this->request->subject?->credits ?? ''],
            ['Học kỳ:', $this->request->semester?->name ?? ''],
            ['Người tạo yêu cầu:', $this->request->student?->user?->name ?? ''],
            ['Mã sinh viên tạo:', $this->request->student?->student_code ?? ''],
            ['Trạng thái:', $this->getStatusLabel($this->request->status)],
            ['Ngày tạo:', $this->request->created_at?->format('d/m/Y H:i') ?? ''],
            ['Ghi chú sinh viên:', $this->request->note ?? ''],
            ['Ghi chú admin:', $this->request->admin_note ?? ''],
            ['Tổng số sinh viên tham gia:', $this->request->students()->count()],
            [], // Empty row
            ['DANH SÁCH SINH VIÊN THAM GIA'],
            [
                'STT',
                'Mã sinh viên',
                'Họ và tên',
                'Lớp',
                'Khoa',
                'Vai trò',
                'Ngày tham gia',
            ]
        ];

        return $requestInfo;
    }

    public function map($student): array
    {
        static $index = 0;
        $index++;
        
        $isCreator = $student->id === $this->request->student_id;
        $role = $isCreator ? 'Người tạo yêu cầu' : 'Người tham gia';
        
        return [
            $index,
            $student->student_code ?? '',
            $student->user?->name ?? '',
            $student->class ?? '',
            $student->faculty ?? '',
            $role,
            $student->pivot?->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    public function title(): string
    {
        $subjectCode = $this->request->subject?->code ?? 'Unknown';
        $semesterName = $this->request->semester?->name ?? 'Unknown';
        return "{$subjectCode} - {$semesterName}";
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header information section
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F81BD');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');

        // Style the student list header
        $sheet->mergeCells('A14:G14');
        $sheet->getStyle('A14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A14')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8DB4E2');

        // Style the column headers
        $sheet->getStyle('A15:G15')->getFont()->setBold(true);
        $sheet->getStyle('A15:G15')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCE6F1');
        $sheet->getStyle('A15:G15')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add borders to the data section
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 15) {
            $sheet->getStyle("A15:G{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Make information labels bold
        $sheet->getStyle('A2:A12')->getFont()->setBold(true);

        return $sheet;
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'cancelled' => 'Đã hủy',
            default => $status,
        };
    }
}