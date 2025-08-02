<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Quản lý phản hồi';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Phản hồi';
    protected static ?string $pluralLabel = 'Phản hồi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin phản hồi')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Học sinh')
                            ->relationship('student', 'student_code')
                            ->getOptionLabelFromRecordUsing(fn (Student $record) => $record->user->name . ' (' . $record->student_code . ')')
                            ->searchable(['student_code'])
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('category')
                            ->label('Danh mục')
                            ->options([
                                'academic' => 'Học tập',
                                'facility' => 'Cơ sở vật chất',
                                'service' => 'Dịch vụ',
                                'other' => 'Khác',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả chi tiết')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('tag')
                            ->label('Thẻ phân loại')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'pending' => 'Chờ xử lý',
                                'reviewed' => 'Đã xem xét',
                                'resolved' => 'Đã giải quyết',
                            ])
                            ->default('pending')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Học sinh')
                    ->description(fn (Feedback $record): string => $record->student->student_code ?? '')
                    ->searchable(['student.user.name', 'student.student_code'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('category')
                    ->label('Danh mục')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'academic' => 'info',
                        'facility' => 'warning',
                        'service' => 'success',
                        'other' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'academic' => 'Học tập',
                        'facility' => 'Cơ sở vật chất',
                        'service' => 'Dịch vụ',
                        'other' => 'Khác',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewed' => 'info',
                        'resolved' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ xử lý',
                        'reviewed' => 'Đã xem xét',
                        'resolved' => 'Đã giải quyết',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('tag')
                    ->label('Thẻ')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'reviewed' => 'Đã xem xét',
                        'resolved' => 'Đã giải quyết',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Danh mục')
                    ->options([
                        'academic' => 'Học tập',
                        'facility' => 'Cơ sở vật chất',
                        'service' => 'Dịch vụ',
                        'other' => 'Khác',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListFeedbacks::route('/'),
            'create' => Pages\CreateFeedback::route('/create'),
            'view' => Pages\ViewFeedback::route('/{record}'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}