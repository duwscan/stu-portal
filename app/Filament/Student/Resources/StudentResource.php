<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\StudentResource\Pages;
use App\Filament\Student\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Học sinh';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput('student_code')
                    ->label('Mã sinh viên')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\DatePicker('birthday')
                    ->label('Ngày sinh'),
                Forms\Components\Select('gender')
                    ->label('Giới tính')
                    ->options([
                        'Nam' => 'Nam',
                        'Nữ' => 'Nữ',
                        'Khác' => 'Khác',
                    ]),
                Forms\Components\TextInput('phone')
                    ->label('Số điện thoại'),
                Forms\Components\TextInput('class')
                    ->label('Lớp'),
                Forms\Components\TextInput('faculty')
                    ->label('Khoa'),
                Forms\Components\TextInput('address')
                    ->label('Địa chỉ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_code')->label('Mã sinh viên')->searchable(),
                Tables\Columns\TextColumn::make('birthday')->label('Ngày sinh')->date(),
                Tables\Columns\TextColumn::make('gender')->label('Giới tính'),
                Tables\Columns\TextColumn::make('phone')->label('Số điện thoại'),
                Tables\Columns\TextColumn::make('class')->label('Lớp'),
                Tables\Columns\TextColumn::make('faculty')->label('Khoa'),
                Tables\Columns\TextColumn::make('address')->label('Địa chỉ'),
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
}
