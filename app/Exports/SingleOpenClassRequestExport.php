<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\OpenClassRequest;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SingleOpenClassRequestExport implements FromArray, ShouldAutoSize, WithStyles
{
    private OpenClassRequest $request;

    public function __construct(OpenClassRequest $request)
    {
        $this->request = $request->load(['subject', 'student.user', 'semester', 'students.user']);
    }

    public function array(): array
    {
        $data = [];
        
        // Request information headers
        $data[] = ['THÔNG TIN YÊU CẦU MỞ LỚP'];
        $data[] = ['Môn học:', $this->request->subject?->name ?? ''];
        $data[] = ['Mã môn học:', $this->request->subject?->code ?? ''];
        $data[] = ['Số tín chỉ:', $this->request->subject?->credits ?? ''];
        $data[] = ['Học kỳ:', $this->request->semester?->name ?? ''];
        $data[] = ['Người tạo yêu cầu:', $this->request->student?->user?->name ?? ''];
        $data[] = ['Mã sinh viên tạo:', $this->request->student?->student_code ?? ''];
        $data[] = ['Trạng thái:', $this->getStatusLabel($this->request->status)];
        $data[] = ['Ngày tạo:', $this->request->created_at?->format('d/m/Y H:i') ?? ''];
        $data[] = ['Ghi chú sinh viên:', $this->request->note ?? ''];
        $data[] = ['Ghi chú admin:', $this->request->admin_note ?? ''];
        $data[] = ['Tổng số sinh viên tham gia:', $this->request->students()->count()];
        $data[] = []; // Empty row
        
        // Student list header
        $data[] = ['DANH SÁCH SINH VIÊN THAM GIA'];
        $data[] = [
            'STT',
            'Mã sinh viên',
            'Họ và tên',
            'Lớp',
            'Khoa',
            'Vai trò',
            'Ngày tham gia',
        ];

        // Student data
        $students = $this->request->students;
        foreach ($students as $index => $student) {
            $isCreator = $student->id === $this->request->student_id;
            $role = $isCreator ? 'Người tạo yêu cầu' : 'Người tham gia';
            
            $data[] = [
                $index + 1,
                $student->student_code ?? '',
                $student->user?->name ?? '',
                $student->class ?? '',
                $student->faculty ?? '',
                $role,
                $student->pivot?->created_at?->format('d/m/Y H:i') ?? '',
            ];
        }

        return $data;
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