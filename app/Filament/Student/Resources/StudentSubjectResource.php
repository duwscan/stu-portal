<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\StudentSubjectResource\Pages;
use App\Models\StudentSubject;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentSubjectResource extends Resource
{
    protected static ?string $model = StudentSubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Điểm môn học';

    protected static ?string $modelLabel = 'Điểm môn học';

    protected static ?string $pluralModelLabel = 'Điểm môn học';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('programSubject.subject.name')
                    ->label('Tên môn học')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('programSubject.subject.credits')
                    ->label('Số tín chỉ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Điểm (Thang 4.0)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('letter_grade')
                    ->label('Điểm chữ')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'A+', 'A' => 'success',
                        'B+', 'B' => 'info',
                        'C+', 'C' => 'warning',
                        'D+', 'D' => 'gray',
                        'F' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'passed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'passed' => 'Qua môn',
                        'failed' => 'Trượt',
                        default => 'Chưa có',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('semester')
                    ->label('Học kỳ')
                    ->options(function () {
                        $student = auth()->user()?->student;
                        if (!$student) {
                            return [];
                        }

                        return $student->trainingProgram->programSubjects()
                            ->distinct()
                            ->orderBy('semester')
                            ->pluck('semester')
                            ->mapWithKeys(fn ($semester) => ["$semester" => "Học kỳ $semester"]);
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $q, $semester): Builder => $q->whereHas(
                                'programSubject',
                                fn (Builder $q) => $q->where('semester', $semester)
                            )
                        );
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'passed' => 'Qua môn',
                        'failed' => 'Trượt',
                    ]),
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
            'index' => Pages\ListStudentSubjects::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $student = auth()->user()?->student;

        if (!$student) {
            return parent::getEloquentQuery()->whereRaw('1 = 0'); // Return empty query if no student
        }
        return parent::getEloquentQuery()
            ->where('student_id', $student->id)
            ->withWhereHas('programSubject', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with(['programSubject','programSubject.subject', 'programSubject.trainingProgram']);
    }
}
