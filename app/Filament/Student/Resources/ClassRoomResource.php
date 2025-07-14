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

    protected static ?string $navigationLabel = 'Lớp học phần đã đăng ký';

    protected static ?string $modelLabel = 'Lớp học phần đã đăng ký';

    protected static ?string $pluralModelLabel = 'Lớp học phần đã đăng ký';

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
                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Học kỳ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_date')
                    ->label('Ngày đăng ký')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('registration_date', 'desc')
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
        $student = auth()->user()?->student;

        if (!$student) {
            return parent::getEloquentQuery()->whereRaw('1 = 0'); // Return empty query if no student
        }

        return parent::getEloquentQuery()
            ->select('class_rooms.*', 'class_room_student.created_at as registration_date')
            ->join('class_room_student', 'class_rooms.id', '=', 'class_room_student.class_room_id')
            ->where('class_room_student.student_id', $student->id)
            ->with(['subject', 'semester']);
    }
}
