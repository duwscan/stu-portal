<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramSubjectResource\Pages;
use App\Models\ProgramSubject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgramSubjectResource extends Resource
{
    protected static ?string $model = ProgramSubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Quản lý đào tạo';

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Môn học';
    protected static ?string $pluralLabel = 'Môn học';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin môn học')
                    ->description('Chọn chương trình đào tạo và môn học')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('training_program_id')
                            ->label('Chương trình đào tạo')
                            ->relationship('trainingProgram', 'name')
                            ->required()
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
                            ->columnSpan(1),
                        Forms\Components\Select::make('subject_id')
                            ->label('Môn học')
                            ->relationship('subject', 'name', function ($query, $get) {
                                $selectedSubject = $get('subject_id');
                                $excludedIds = \App\Models\ProgramSubject::query()
                                    ->where('training_program_id', $get('training_program_id'));
                                if($selectedSubject) {
                                    $excludedIds->where('subject_id', '!=', $selectedSubject);
                                }
                                return $query->whereNotIn(
                                'id',
                                $excludedIds->pluck('subject_id'));
                            })
                            ->disabled('edit')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('code')
                                    ->label('Mã môn học')
                                    ->required()
                                    ->unique('subjects', 'code')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name')
                                    ->label('Tên môn học')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('credits')
                                    ->label('Số tín chỉ')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->maxValue(10),
                            ])
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('semester')
                            ->label('Học kỳ')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1)
                            ->maxValue(8)
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('is_required')
                            ->label('Môn bắt buộc')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kích hoạt')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),
                    ]),
                Forms\Components\Section::make('Môn học tiên quyết')
                    ->description('Chọn các môn học trong chương trình đào tạo tiên quyết')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->schema([
                        Forms\Components\Select::make('prerequisites')
                            ->label('Môn học tiên quyết')
                            ->multiple()
                            ->relationship(
                                'prerequisites',
                                'id',
                                fn ($query, $get) => $query->select('program_subjects.*')
                                    ->join('subjects', 'subjects.id', '=', 'program_subjects.subject_id')
                                    ->where('program_subjects.training_program_id', $get('training_program_id'))
                                    ->where('program_subjects.id', '!=', $get('id'))
                                    ->where('subjects.id', '!=', $get('subject_id'))
                                    ->orderBy('subjects.name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->subject?->name)
                            ->searchable()
                            ->preload()
                    ]),
                Forms\Components\Section::make('Môn học đồng thời')
                    ->description('Chọn các môn học trong chương trình đào tạo học đồng thời')
                    ->icon('heroicon-o-arrows-right-left')
                    ->schema([
                        Forms\Components\Select::make('corequisites')
                            ->label('Môn học đồng thời')
                            ->multiple()
                            ->relationship(
                                'corequisites',
                                'id',
                                fn ($query, $get) => $query->select('program_subjects.*')
                                    ->join('subjects', 'subjects.id', '=', 'program_subjects.subject_id')
                                    ->where('program_subjects.training_program_id', $get('training_program_id'))
                                    ->where('program_subjects.id', '!=', $get('id'))
                                    ->where('subjects.id', '!=', $get('subject_id'))
                                    ->orderBy('subjects.name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->subject?->name)
                            ->searchable()
                            ->preload()
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('semester')
            ->columns([
                Tables\Columns\TextColumn::make('trainingProgram.name')
                    ->label('Chương trình đào tạo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Môn học')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.credits')
                    ->label('Số tín chỉ')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('semester')
                    ->label('Học kỳ')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('Bắt buộc')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('prerequisites.subject.name')
                    ->label('Môn học tiên quyết')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('prerequisites.subject', function (Builder $query) use ($search) {
                                $query->where('subjects.name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('subject', function (Builder $query) use ($search) {
                                $query->where('subjects.name', 'like', "%{$search}%");
                            });
                    }),
                Tables\Columns\TextColumn::make('corequisites.subject.name')
                    ->label('Môn học đồng thời')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->whereHas('corequisites.subject', function (Builder $query) use ($search) {
                                $query->where('subjects.name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('subject', function (Builder $query) use ($search) {
                                $query->where('subjects.name', 'like', "%{$search}%");
                            });
                    })
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('training_program_id')
                    ->label('Chương trình đào tạo')
                    ->relationship('trainingProgram', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Môn bắt buộc')
                    ->boolean()
                    ->trueLabel('Bắt buộc')
                    ->falseLabel('Tự chọn'),
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
                        ->modalDescription('Bạn có chắc chắn muốn xóa các môn học đã chọn?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm môn học vào chương trình'),
            ])
            ->emptyStateDescription('Chưa có môn học nào trong chương trình.')
            ->emptyStateHeading('Chưa có dữ liệu')
            ->emptyStateIcon('heroicon-o-academic-cap');
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
            'index' => Pages\ListProgramSubjects::route('/'),
            'create' => Pages\CreateProgramSubject::route('/create'),
            'edit' => Pages\EditProgramSubject::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select('program_subjects.*') // Chỉ định rõ các cột cần select
            ->with(['trainingProgram', 'subject', 'prerequisites.subject', 'corequisites.subject']); // Eager load các relationship
    }
}
