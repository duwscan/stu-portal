<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\StudentGradeResource\Pages;
use App\Models\StudentSubject;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentGradeResource extends Resource
{
    protected static ?string $model = StudentSubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Quản lý học tập';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Điểm học sinh';

    protected static ?string $modelLabel = 'Điểm học sinh';

    protected static ?string $pluralModelLabel = 'Điểm học sinh';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Tên học sinh')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.student_code')
                    ->label('Mã học sinh')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.class')
                    ->label('Lớp')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('programSubject.subject.code')
                    ->label('Mã môn học')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('programSubject.subject.name')
                    ->label('Tên môn học')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('programSubject.subject.credits')
                    ->label('Số tín chỉ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('programSubject.semester')
                    ->label('Học kỳ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Điểm (Thang 4.0)')
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 2,
                    ),
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
                    ->color(fn (?string $state): string => match ($state) {
                        'passed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'passed' => 'Qua môn',
                        'failed' => 'Trượt',
                        null => 'Chưa có',
                        default => 'Chưa có',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cập nhật lần cuối')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('student.user.name', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('subject')
                    ->label('Môn học')
                    ->relationship('programSubject.subject', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('semester')
                    ->label('Học kỳ')
                    ->options(function () {
                        return \App\Models\ProgramSubject::distinct()
                            ->orderBy('semester')
                            ->pluck('semester', 'semester')
                            ->mapWithKeys(fn ($semester) => [$semester => "Học kỳ $semester"]);
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'passed' => 'Qua môn',
                        'failed' => 'Trượt',
                    ]),
                Tables\Filters\Filter::make('has_grade')
                    ->label('Có điểm')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('grade')),
                Tables\Filters\Filter::make('no_grade')
                    ->label('Chưa có điểm')
                    ->query(fn (Builder $query): Builder => $query->whereNull('grade')),
                Tables\Filters\SelectFilter::make('class')
                    ->label('Lớp')
                    ->options(function () {
                        return \App\Models\Student::distinct()
                            ->orderBy('class')
                            ->pluck('class', 'class');
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $q, $class): Builder => $q->whereHas(
                                'student',
                                fn (Builder $q) => $q->where('class', $class)
                            )
                        );
                    }),
            ])
            ->actions([
                // Read-only resource - no actions for now
            ])
            ->bulkActions([
                // Read-only resource - no bulk actions for now
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentGrades::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'student',
                'student.user',
                'programSubject',
                'programSubject.subject',
                'programSubject.trainingProgram',
            ])
            ->whereHas('programSubject', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereHas('student');
    }

    public static function canCreate(): bool
    {
        return false; // Read-only resource
    }
}