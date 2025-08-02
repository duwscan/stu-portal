<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassRoomResource\Pages;
use App\Models\ClassRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Quản lý đào tạo';

    protected static ?string $label = 'Lớp học';
    protected static ?string $pluralLabel = 'Lớp học';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Mã lớp')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->label('Giảng viên')
                    ->relationship('teacher', 'name', function (Builder $query) {
                        return $query->role([
                            'teacher',
                            'admin',
                        ]);
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('subject_id')
                    ->label('Môn học')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('semester_id')
                    ->label('Kỳ học')
                    ->relationship(
                        'semester',
                        'name',
                        fn ($query) => $query->orderBy('start_date', 'desc')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->formatted_name)
                    ->default(function () {
                        return \App\Models\Semester::query()
                            ->where('start_date', '<=', now())
                            ->where('end_date', '>=', now())
                            ->orderBy('start_date', 'desc')
                            ->first()?->id;
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('capacity')
                    ->label('Số lượng sinh viên tối đa')
                    ->numeric()
                    ->default(40)
                    ->required()
                    ->minValue(1)
                    ->maxValue(100),
                Forms\Components\Toggle::make('is_open')
                    ->label('Mở đăng ký')
                    ->default(true),
//                Forms\Components\DateTimePicker::make('start_date')
//                    ->label('Thời gian bắt đầu đăng ký')
//                    ->nullable(),
//                Forms\Components\DateTimePicker::make('end_date')
//                    ->label('Thời gian kết thúc đăng ký')
//                    ->nullable(),
                Forms\Components\Select::make('shift')
                    ->label('Ca học')
                    ->options([
                        'morning' => 'Ca sáng',
                        'afternoon' => 'Ca chiều',
                    ])
                    ->nullable()
                    ->native(false),
                Forms\Components\Select::make('day_of_week')
                    ->label('Thứ')
                    ->options([
                        'monday' => 'Thứ 2',
                        'tuesday' => 'Thứ 3',
                        'wednesday' => 'Thứ 4',
                        'thursday' => 'Thứ 5',
                        'friday' => 'Thứ 6',
                        'saturday' => 'Thứ 7',
                        'sunday' => 'Chủ nhật',
                    ])
                    ->nullable()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã lớp')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Giảng viên')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Môn học')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.formatted_name')
                    ->label('Kỳ học')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('semester', function (Builder $query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhereRaw("strftime('%m/%Y', start_date) like ?", ["%{$search}%"])
                                ->orWhereRaw("strftime('%m/%Y', end_date) like ?", ["%{$search}%"]);
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            \App\Models\Semester::select('start_date')
                                ->whereColumn('semesters.id', 'class_rooms.semester_id'),
                            $direction
                        );
                    }),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Số lượng SV tối đa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_open')
                    ->label('Mở đăng ký')
                    ->boolean(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Bắt đầu đăng ký')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Chưa thiết lập'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Kết thúc đăng ký')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Chưa thiết lập'),
                Tables\Columns\TextColumn::make('shift_name')
                    ->label('Ca học')
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_of_week_name')
                    ->label('Thứ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Kỳ học')
                    ->relationship(
                        'semester',
                        'formatted_name',
                        fn ($query) => $query->orderBy('start_date', 'desc')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->formatted_name)
                    ->default(function () {
                        return \App\Models\Semester::query()
                            ->where('start_date', '<=', now())
                            ->where('end_date', '>=', now())
                            ->orderBy('start_date', 'desc')
                            ->first()?->id;
                    })
                    ->preload(),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Môn học')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Giảng viên')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_open')
                    ->label('Trạng thái đăng ký')
                    ->boolean()
                    ->trueLabel('Mở đăng ký')
                    ->falseLabel('Đóng đăng ký')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassRooms::route('/'),
            'create' => Pages\CreateClassRoom::route('/create'),
            'edit' => Pages\EditClassRoom::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['teacher', 'subject', 'semester']);
    }
}
