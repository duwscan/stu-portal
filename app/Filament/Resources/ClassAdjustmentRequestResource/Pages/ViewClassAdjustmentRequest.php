<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClassAdjustmentRequestResource\Pages;

use App\Filament\Resources\ClassAdjustmentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class ViewClassAdjustmentRequest extends ViewRecord
{
    protected static string $resource = ClassAdjustmentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin sinh viên')
                    ->schema([
                        TextEntry::make('student.user.name')
                            ->label('Tên sinh viên')
                            ->columnSpan(1),
                        TextEntry::make('student.student_code')
                            ->label('Mã sinh viên')
                            ->state(fn ($record): string => $record->student?->student_code ?? 'Không có')
                            ->columnSpan(1),
                        TextEntry::make('student.trainingProgram.name')
                            ->label('Chương trình đào tạo')
                            ->columnSpan(1),
                        TextEntry::make('semester.name')
                            ->label('Học kỳ')
                            ->columnSpan(1),
                        TextEntry::make('created_at')
                            ->label('Ngày tạo yêu cầu')
                            ->dateTime('d/m/Y H:i')
                            ->columnSpan(1),
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Đang chờ',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối',
                                default => $state,
                            })
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->collapsible(),
                Section::make('Thông tin điều chỉnh lớp')
                    ->schema([
                        TextEntry::make('type')
                            ->label('Loại yêu cầu')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'change_class' => 'info',
                                'add_class' => 'success',
                                'drop_class' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cancel' => 'Hủy lớp học',
                                'change' => 'Đổi lớp học',
                                'register' => 'Đăng ký lớp học',
                                default => $state,
                            })
                            ->columnSpan(1),
                                                TextEntry::make('fromClass.code')
                            ->label('Từ lớp')
                            ->state(fn ($record): string =>
                                $record->fromClass?->subject?->name ? "{$record->fromClass->code} - {$record->fromClass->subject->name}" : ($record->fromClass?->code ?? 'Không có')
                            )
                            ->columnSpan(1),
                        TextEntry::make('toClass.code')
                            ->label('Đến lớp')
                            ->state(fn ($record): string =>
                                $record->toClass?->subject?->name ? "{$record->toClass->code} - {$record->toClass->subject->name}" : ($record->toClass?->code ?? 'Không có')
                            )
                            ->columnSpan(1),
                        TextEntry::make('reason')
                            ->label('Lý do')
                            ->columnSpan(3),
                        TextEntry::make('admin_note')
                            ->label('Ghi chú của admin')
                            ->visible(fn ($record): bool => !empty($record->admin_note))
                            ->columnSpan(3),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
