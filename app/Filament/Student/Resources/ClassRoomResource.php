<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\ClassRoomResource\Pages;
use App\Models\ClassRoom;
use App\Models\Semester;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Lớp học phần';

    protected static ?string $modelLabel = 'Lớp học phần';

    protected static ?string $pluralModelLabel = 'Lớp học phần';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Tên môn học')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã lớp')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.credits')
                    ->label('Số tín chỉ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registered_count')
                    ->label('Số lượng đã đăng ký')
                    ->formatStateUsing(fn(ClassRoom $record): string => "{$record->registered_count}/{$record->capacity}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Học kỳ')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_open')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('subject.name')
            ->filters([
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Học kỳ')
                    ->options(fn () => Semester::query()->pluck('name', 'id'))
                    ->default(fn () => Semester::getCurrentSemester()?->id)
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
            'index' => Pages\ListClassRooms::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('semester', function (Builder $query) {
                $query->where('id', Semester::getCurrentSemester()?->id);
            })
            ->with(['subject', 'semester']);
    }
}
