<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\OpenClassRoomResource\Pages;
use App\Models\ClassRoom;
use App\Models\ProgramSubject;
use App\Models\Semester;
use App\Models\StudentSubject;
use App\Filament\Student\Tables\Columns\RegisterStatusColumn;
use App\ValueObjects\ClassRegisterStatus;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
                    ->description(fn(ClassRoom $record): string => "Mã lớp: {$record->code}")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.credits')
                    ->label('Số tín chỉ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registered_count')
                    ->label('Số lượng đã đăng ký')
                    ->formatStateUsing(fn(ClassRoom $record): string => "{$record->registered_count}/{$record->capacity}")
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_open')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
//                Tables\Columns\TextColumn::make('start_date')
//                    ->label('Bắt đầu đăng ký')
//                    ->dateTime('d/m/Y H:i')
//                    ->sortable()
//                    ->placeholder('Chưa thiết lập'),
//                Tables\Columns\TextColumn::make('end_date')
//                    ->label('Kết thúc đăng ký')
//                    ->dateTime('d/m/Y H:i')
//                    ->sortable()
//                    ->placeholder('Chưa thiết lập'),
                Tables\Columns\TextColumn::make('shift_name')
                    ->label('Ca học')
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_of_week_name')
                    ->label('Thứ')
                    ->sortable(),
                RegisterStatusColumn::make('register_status')
                    ->label('Trạng thái đăng ký')

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
