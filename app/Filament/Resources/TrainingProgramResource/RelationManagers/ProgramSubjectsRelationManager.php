<?php

namespace App\Filament\Resources\TrainingProgramResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgramSubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'programSubjects';

    protected static ?string $label = 'Môn học';
    protected static ?string $labelSingular = 'Môn học';
    protected static ?string $pluralLabel = 'Môn học';
    protected static ?string $recordTitleAttribute = 'subject.name';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('semester')
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Tên môn học')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.code')
                    ->label('Mã môn học')
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
                    ->bulleted(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Môn bắt buộc')
                    ->boolean()
                    ->trueLabel('Bắt buộc')
                    ->falseLabel('Tự chọn'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm môn học')
                    ->modalHeading('Thêm môn học vào chương trình')
                    ->form([
                        Forms\Components\Select::make('subject_id')
                            ->label('Môn học')
                            ->relationship('subject', 'name', function ($query, $get) {
                                return $query->whereNotIn(
                                    'id',
                                    \App\Models\ProgramSubject::query()
                                        ->where('training_program_id', $this->getOwnerRecord()->id)
                                        ->pluck('subject_id')
                                );
                            })
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
                            ]),
                        Forms\Components\Toggle::make('is_required')
                            ->label('Môn bắt buộc')
                            ->default(true)
                            ->inline(false),
                        Forms\Components\Select::make('prerequisites')
                            ->label('Môn học tiên quyết')
                            ->multiple()
                            ->relationship(
                                'prerequisites',
                                'id',
                                fn ($query, $get) => $query->select('program_subjects.*')
                                    ->join('subjects', 'subjects.id', '=', 'program_subjects.subject_id')
                                    ->where('program_subjects.training_program_id', $this->getOwnerRecord()->id)
                                    ->where('subjects.id', '!=', $get('subject_id'))
                                    ->orderBy('subjects.name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->subject?->name)
                            ->searchable()
                            ->preload(),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Chỉnh sửa môn học')
                    ->form([
                        Forms\Components\TextInput::make('semester')
                            ->label('Học kỳ')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1)
                            ->maxValue(10),
                        Forms\Components\Toggle::make('is_required')
                            ->label('Môn bắt buộc')
                            ->default(true)
                            ->inline(false),
                        Forms\Components\Select::make('prerequisites')
                            ->label('Môn học tiên quyết')
                            ->multiple()
                            ->relationship(
                                'prerequisites',
                                'id',
                                fn ($query, $get) => $query->select('program_subjects.*')
                                    ->join('subjects', 'subjects.id', '=', 'program_subjects.subject_id')
                                    ->where('program_subjects.training_program_id', $this->getOwnerRecord()->id)
                                    ->where('program_subjects.id', '!=', $get('id'))
                                    ->where('subjects.id', '!=', $get('subject_id'))
                                    ->orderBy('subjects.name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->subject?->name)
                            ->searchable()
                            ->preload(),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Bạn có chắc chắn muốn xóa các môn học đã chọn?'),
            ]);
    }
}
