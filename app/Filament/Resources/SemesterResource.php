<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SemesterResource\Pages;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SemesterResource extends Resource
{
    protected static ?string $model = Semester::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $label = 'Kỳ học';
    protected static ?string $pluralLabel = 'Kỳ học';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên kỳ học')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Ngày bắt đầu')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Ngày kết thúc')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Số sinh viên')
                    ->counts('students')
                    ->alignCenter(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSemesters::route('/'),
            'create' => Pages\CreateSemester::route('/create'),
            'edit' => Pages\EditSemester::route('/{record}/edit'),
        ];
    }
}
