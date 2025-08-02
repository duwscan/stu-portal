<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Phản hồi của tôi';

    protected static ?string $modelLabel = 'Phản hồi';

    protected static ?string $pluralModelLabel = 'Phản hồi';

    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin phản hồi')
                    ->description('Gửi phản hồi của bạn đến ban quản lý')
                    ->schema([
                        Forms\Components\Hidden::make('student_id')
                            ->default(function () {
                                return Auth::user()?->student?->id;
                            }),
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
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả chi tiết')
                            ->required()
                            ->rows(4),
                        Forms\Components\TextInput::make('tag')
                            ->label('Thẻ phân loại (tùy chọn)')
                            ->maxLength(255)
                            ->helperText('Nhập từ khóa để phân loại phản hồi'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
                Tables\Actions\EditAction::make()
                    ->visible(fn (Feedback $record): bool => $record->status === 'pending'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Feedback $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            // Only allow deletion of pending feedbacks
                            $records->where('status', 'pending')->each->delete();
                        }),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        // Only show feedbacks from the current student
        return parent::getEloquentQuery()->where('student_id', Auth::user()?->student?->id);
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