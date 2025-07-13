<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Quản lý người dùng';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Học sinh';
    protected static ?string $pluralLabel = 'Học sinh';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin tài khoản')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(table: 'users', ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ]),
                Forms\Components\Section::make('Thông tin học sinh')
                    ->relationship('student')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('student_code')
                            ->label('Mã sinh viên')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Ngày sinh')
                            ->required(),
                        Forms\Components\Select::make('gender')
                            ->label('Giới tính')
                            ->options([
                                'male' => 'Nam',
                                'female' => 'Nữ',
                                'other' => 'Khác',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('class')
                            ->label('Lớp')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('training_program_id')
                            ->label('Chương trình đào tạo')
                            ->relationship('trainingProgram', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('code')
                                    ->label('Mã chương trình')
                                    ->required()
                                    ->unique('training_programs', 'code')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name')
                                    ->label('Tên chương trình')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('address')
                            ->label('Địa chỉ')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                \Filament\Tables\Actions\Action::make('updateSemester')
                    ->label('Cập nhật kỳ học')
                    ->icon('heroicon-o-academic-cap')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên kỳ học')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Ngày bắt đầu')
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Ngày kết thúc')
                            ->required()
                            ->native(false)
                            ->after('start_date'),
                    ])
                    ->action(function (array $data) {
                        $semester = \App\Models\Semester::create([
                            'name' => $data['name'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                        ]);
                        $students = \App\Models\Student::all();

                        foreach ($students as $student) {
                            $student->semesters()->syncWithoutDetaching([$semester->id]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Cập nhật thành công')
                            ->success()
                            ->send();
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.student_code')
                    ->label('Mã sinh viên')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.class')
                    ->label('Lớp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.trainingProgram.name')
                    ->label('Chương trình đào tạo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'student',
                'student.trainingProgram',
                'student.semesters' => function ($query) {
                    $query->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->orderBy('start_date', 'desc');
                },
            ])
            ->role(['student']);
    }
}
