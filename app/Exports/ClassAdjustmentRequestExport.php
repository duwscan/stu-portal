<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\ClassAdjustmentRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class ClassAdjustmentRequestExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection(): Collection
    {
        return ClassAdjustmentRequest::with([
            'student.user',
            'semester', 
            'fromClass.subject',
            'toClass.subject'
        ])->get();
    }

    public function headings(): array
    {
        return [
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
    }

    public function map($classAdjustmentRequest): array
    {
        return [
            $classAdjustmentRequest->student?->student_code ?? '',
            $classAdjustmentRequest->student?->user?->name ?? '',
            $classAdjustmentRequest->semester?->name ?? '',
            $classAdjustmentRequest->fromClass?->code ?? '',
            $classAdjustmentRequest->fromClass?->subject?->name ?? '',
            $classAdjustmentRequest->toClass?->code ?? '',
            $classAdjustmentRequest->toClass?->subject?->name ?? '',
            $this->getTypeLabel($classAdjustmentRequest->type),
            $this->getStatusLabel($classAdjustmentRequest->status),
            $classAdjustmentRequest->reason ?? '',
            $classAdjustmentRequest->admin_note ?? '',
            $classAdjustmentRequest->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    private function getTypeLabel(string $type): string
    {
        return match($type) {
            'change_class' => 'Chuyển lớp',
            'add_class' => 'Thêm lớp', 
            'drop_class' => 'Bỏ lớp',
            'cancel' => 'Hủy lớp học',
            'change' => 'Đổi lớp học',
            'register' => 'Đăng ký lớp học',
            default => $type,
        };
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            default => $status,
        };
    }
}