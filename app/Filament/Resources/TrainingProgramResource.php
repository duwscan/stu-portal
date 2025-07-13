<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingProgramResource\Pages;
use App\Filament\Resources\TrainingProgramResource\RelationManagers;
use App\Models\TrainingProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrainingProgramResource extends Resource
{
    protected static ?string $model = TrainingProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Quản lý đào tạo';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Chương trình đào tạo';
    protected static ?string $pluralLabel = 'Chương trình đào tạo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->description('Nhập các thông tin cơ bản của chương trình đào tạo')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Mã chương trình')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Ví dụ: CNTT2024')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('name')
                            ->label('Tên chương trình')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ví dụ: Công nghệ thông tin')
                            ->columnSpan(1),
                        Forms\Components\Select::make('degree_type')
                            ->label('Loại bằng cấp')
                            ->options([
                                'bachelor' => 'Cử nhân',
                                'engineer' => 'Kỹ sư',
                                'master' => 'Thạc sĩ',
                                'doctor' => 'Tiến sĩ',
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('specialization')
                            ->label('Chuyên ngành')
                            ->maxLength(255)
                            ->placeholder('Ví dụ: Phát triển phần mềm')
                            ->columnSpan(1),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('duration_years')
                                    ->label('Thời gian đào tạo (năm)')
                                    ->numeric()
                                    ->default(4)
                                    ->required()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->step(0.5),
                            ])
                            ->columnSpan(2),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Đang hoạt động')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(2),
                    ]),
                Forms\Components\Section::make('Mô tả chi tiết')
                    ->description('Mô tả chi tiết về chương trình đào tạo')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Mô tả')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã chương trình')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Đã sao chép mã chương trình')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên chương trình')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('degree_type')
                    ->label('Loại bằng cấp')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bachelor' => 'info',
                        'engineer' => 'success',
                        'master' => 'warning',
                        'doctor' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bachelor' => 'Cử nhân',
                        'engineer' => 'Kỹ sư',
                        'master' => 'Thạc sĩ',
                        'doctor' => 'Tiến sĩ',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('specialization')
                    ->label('Chuyên ngành')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('program_subjects_sum_credits')
                    ->label('Tổng số tín chỉ')
                    ->state(function ($record) {
                        return $record->programSubjects()
                            ->join('subjects', 'subjects.id', '=', 'program_subjects.subject_id')
                            ->sum('subjects.credits');
                    })
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('degree_type')
                    ->label('Loại bằng cấp')
                    ->options([
                        'bachelor' => 'Cử nhân',
                        'engineer' => 'Kỹ sư',
                        'master' => 'Thạc sĩ',
                        'doctor' => 'Tiến sĩ',
                    ])
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Đang hoạt động')
                    ->boolean()
                    ->trueLabel('Đang hoạt động')
                    ->falseLabel('Không hoạt động')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Thao tác')
                ->icon('heroicon-m-chevron-down')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('Bạn có chắc chắn muốn xóa các chương trình đào tạo đã chọn?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm chương trình đào tạo'),
            ])
            ->emptyStateDescription('Chưa có chương trình đào tạo nào được tạo.')
            ->emptyStateHeading('Chưa có dữ liệu')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProgramSubjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainingPrograms::route('/'),
            'create' => Pages\CreateTrainingProgram::route('/create'),
            'edit' => Pages\EditTrainingProgram::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
