<?php

declare(strict_types=1);

namespace App\Filament\Resources\OpenClassRequestResource\Pages;

use App\Filament\Resources\OpenClassRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class ViewOpenClassRequest extends ViewRecord
{
    protected static string $resource = OpenClassRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Xuất Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\SingleOpenClassRequestExport($this->record),
                        'yeu-cau-mo-lop-' . $this->record->subject?->code . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                    );
                }),
            // Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin yêu cầu')
                    ->schema([
                        TextEntry::make('subject.name')
                            ->label('Môn học')
                            ->columnSpan(1),
                        TextEntry::make('subject.code')
                            ->label('Mã môn học')
                            ->columnSpan(1),
                        TextEntry::make('subject.credits')
                            ->label('Số tín chỉ')
                            ->columnSpan(1),
                        TextEntry::make('semester.name')
                            ->label('Học kỳ')
                            ->columnSpan(1),
                        TextEntry::make('student.user.name')
                            ->label('Người tạo yêu cầu')
                            ->columnSpan(1),
                        TextEntry::make('student.student_code')
                            ->label('Mã sinh viên')
                            ->state(fn ($record): string => $record->student?->student_code ?? 'Không có')
                            ->columnSpan(1),
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Đang chờ',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối',
                                'cancelled' => 'Đã hủy',
                                default => $state,
                            })
                            ->columnSpan(1),
                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i')
                            ->columnSpan(1),
                        TextEntry::make('note')
                            ->label('Ghi chú của sinh viên')
                            ->visible(fn ($record): bool => !empty($record->note))
                            ->columnSpan(3),
                        TextEntry::make('admin_note')
                            ->label('Ghi chú của admin')
                            ->visible(fn ($record): bool => !empty($record->admin_note))
                            ->columnSpan(3),
                    ])
                    ->columns(3)
                    ->collapsible(),
                Section::make('Danh sách sinh viên tham gia')
                    ->schema([
                        TextEntry::make('students_count')
                            ->label('Tổng số sinh viên tham gia')
                            ->state(fn ($record): int => $record->students()->count())
                            ->columnSpan(1),
                        TextEntry::make('students_list')
                            ->label('Danh sách sinh viên')
                            ->html()
                            ->state(function ($record): string {
                                $students = $record->students()
                                    ->with('user')
                                    ->orderBy('created_at')
                                    ->get();

                                if ($students->isEmpty()) {
                                    return '<span class="text-gray-500">Chưa có sinh viên nào tham gia</span>';
                                }

                                                                $html = '<div class="space-y-4">';
                                foreach ($students as $index => $student) {
                                    $isCreator = $student->id === $record->student_id;
                                    $creatorBadge = $isCreator ? '<span class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full ml-3">Người tạo</span>' : '';

                                    $html .= '<div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">';
                                    $html .= '<div class="flex items-center space-x-4">';
                                    $html .= '<span class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-blue-500 rounded-full">' . ($index + 1) . '</span>';
                                    $html .= '<div class="space-y-1">';
                                    $html .= '<div class="font-semibold text-gray-900 text-base">' . $student->user->name . '</div>';
                                    $html .= '<div class="text-sm text-gray-600">Mã SV: <span class="font-medium">' . $student->student_code . '</span></div>';
                                    $html .= '</div>';
                                    $html .= '</div>';
                                    $html .= '<div class="flex items-center space-x-3">';
                                    $html .= $creatorBadge;
                                    $html .= '<span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">' . $student->pivot->created_at->format('d/m/Y H:i') . '</span>';
                                    $html .= '</div>';
                                    $html .= '</div>';
                                }
                                $html .= '</div>';

                                return $html;
                            })
                            ->columnSpan(3),
                    ])
                    ->columns(3),
            ]);
    }
}
