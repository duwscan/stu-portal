<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\OpenClassRoomResource\Pages;
use App\Models\ClassRoom;
use App\Models\Semester;
use App\Models\StudentSubject;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OpenClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Đăng ký lớp học phần';

    protected static ?string $modelLabel = 'Lớp học phần đang mở';

    protected static ?string $pluralModelLabel = 'Lớp học phần đang mở';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Tên môn học')
                    ->description(fn (ClassRoom $record): string => "Mã lớp: {$record->code}")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.credits')
                    ->label('Số tín chỉ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registered_count')
                    ->label('Số lượng đã đăng ký')
                    ->formatStateUsing(fn (ClassRoom $record): string => "{$record->registered_count}/{$record->capacity}")
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_open')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('can_register')
                    ->label('Có thể đăng ký')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Có thể đăng ký' : 'Không thể đăng ký')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->description(function (ClassRoom $record): ?string {
                        if ($record->is_full) {
                            return 'Lớp đã đầy';
                        }
                        if (!$record->is_open) {
                            return 'Lớp đã đóng';
                        }
                        if ($record->students()->where('student_id', auth()->user()?->student?->id)->exists()) {
                            return 'Đã đăng ký';
                        }

                        $student = auth()->user()?->student;
                        if (!$student) {
                            return 'Không tìm thấy thông tin sinh viên';
                        }

                        // Kiểm tra môn học có trong chương trình đào tạo không
                        $programSubject = $student->trainingProgram->programSubjects()
                            ->where('subject_id', $record->subject_id)
                            ->where('is_active', true)
                            ->first();

                        if (!$programSubject) {
                            return 'Không nằm trong chương trình đào tạo';
                        }

                        // Kiểm tra các môn tiên quyết
                        $prerequisiteIds = $programSubject->prerequisites->pluck('id');
                        if ($prerequisiteIds->isEmpty()) {
                            return null;
                        }

                        $passedSubjects = StudentSubject::where('student_id', $student->id)
                            ->whereIn('program_subject_id', $prerequisiteIds)
                            ->where('status', 'passed')
                            ->pluck('program_subject_id');

                        $notPassedSubjects = $programSubject->prerequisites()
                            ->whereNotIn('id', $passedSubjects)
                            ->get();

                        if ($notPassedSubjects->isNotEmpty()) {
                            return 'Chưa qua môn: ' . $notPassedSubjects->pluck('subject.name')->join(', ');
                        }

                        return null;
                    }),
            ])
            ->defaultSort('subject.name')
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpenClassRooms::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('semester', function (Builder $query) {
                $query->where('id', Semester::getCurrentSemester()?->id);
            })
            ->with(['subject', 'students']);
    }
}
