<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\ClassAdjustmentRequestResource\Pages;
use App\Models\ClassAdjustmentRequest;
use App\Models\Semester;
use App\Models\ClassRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class ClassAdjustmentRequestResource extends Resource
{
    protected static ?string $model = ClassAdjustmentRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Yêu cầu điều chỉnh lớp học';

    protected static ?string $modelLabel = 'Yêu cầu điều chỉnh lớp học';

    protected static ?string $pluralModelLabel = 'Yêu cầu điều chỉnh lớp học';

    public static function form(Form $form): Form
    {
        $student = auth()->user()?->student;
        $currentSemester = Semester::getCurrentSemester();

        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Loại yêu cầu')
                    ->options([
                        'cancel' => 'Hủy lớp học',
                        'change' => 'Đổi lớp học',
                        'register' => 'Đăng ký lớp học',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('semester_id')
                    ->label('Học kỳ')
                    ->options(fn () => Semester::query()->pluck('name', 'id'))
                    ->default(fn () => $currentSemester?->id)
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Select::make('from_class_id')
                    ->label('Lớp học muốn hủy/đổi')
                    ->options(function () use ($student, $currentSemester) {
                        if (!$student || !$currentSemester) {
                            return [];
                        }
                        // Lấy danh sách lớp đã đăng ký trong học kỳ hiện tại
                        return $student->classRooms()
                            ->where('semester_id', $currentSemester->id)
                            ->with('subject')
                            ->get()
                            ->mapWithKeys(function ($classRoom) {
                                return [
                                    $classRoom->id => "{$classRoom->subject->name} (Mã lớp: {$classRoom->code})"
                                ];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->visible(fn (Forms\Get $get): bool => in_array($get('type'), ['cancel', 'change'])),
                Forms\Components\Select::make('to_class_id')
                    ->label('Lớp học muốn đăng ký')
                    ->options(function () use ($student, $currentSemester) {
                        if (!$student || !$currentSemester) {
                            return [];
                        }

                        // Lấy danh sách lớp học đang mở trong học kỳ hiện tại
                        // nhưng loại trừ các lớp đã đăng ký
                        $registeredClassIds = $student->classRooms()
                            ->where('semester_id', $currentSemester->id)
                            ->pluck('class_rooms.id');

                        return ClassRoom::where('semester_id', $currentSemester->id)
                            ->where('is_open', true)
                            ->whereNotIn('id', $registeredClassIds)
                            ->with('subject')
                            ->get()
                            ->mapWithKeys(function ($classRoom) {
                                return [
                                    $classRoom->id => "{$classRoom->subject->name} (Mã lớp: {$classRoom->code}, Sĩ số: {$classRoom->registered_count}/{$classRoom->capacity})"
                                ];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->visible(fn (Forms\Get $get): bool => in_array($get('type'), ['change', 'register'])),
                Forms\Components\Textarea::make('reason')
                    ->label('Lý do')
                    ->required()
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Loại yêu cầu')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'cancel' => 'Hủy lớp học',
                        'change' => 'Đổi lớp học',
                        'register' => 'Đăng ký lớp học',
                        default => $state ?? '',
                    }),
                Tables\Columns\TextColumn::make('fromClass.subject.name')
                    ->label('Lớp học muốn hủy/đổi')
                    ->description(fn (?ClassAdjustmentRequest $record): ?string =>
                        $record?->fromClass ? "Mã lớp: {$record->fromClass->code}" : null
                    )
                    ->visible(fn (?ClassAdjustmentRequest $record): bool =>
                        $record?->type !== 'register'
                    ),
                Tables\Columns\TextColumn::make('toClass.subject.name')
                    ->label('Lớp học muốn đăng ký')
                    ->description(fn (?ClassAdjustmentRequest $record): ?string =>
                        $record?->toClass ? "Mã lớp: {$record->toClass->code}" : null
                    )
                    ->visible(fn (?ClassAdjustmentRequest $record): bool =>
                        $record?->type !== 'cancel'
                    ),
                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Học kỳ'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Đang chờ',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        default => $state ?? '',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Học kỳ')
                    ->options(fn () => Semester::query()->pluck('name', 'id'))
                    ->default(fn () => Semester::getCurrentSemester()?->id),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại yêu cầu')
                    ->options([
                        'cancel' => 'Hủy lớp học',
                        'change' => 'Đổi lớp học',
                        'register' => 'Đăng ký lớp học',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Đang chờ',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Hủy yêu cầu')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hủy yêu cầu điều chỉnh lớp học')
                    ->modalDescription('Bạn có chắc chắn muốn hủy yêu cầu này?')
                    ->visible(fn (?ClassAdjustmentRequest $record): bool => $record?->status === 'pending')
                    ->action(function (ClassAdjustmentRequest $record) {
                        $record->delete();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin chi tiết')
                    ->schema([
                        TextEntry::make('type')
                            ->label('Loại yêu cầu')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'cancel' => 'Hủy lớp học',
                                'change' => 'Đổi lớp học',
                                'register' => 'Đăng ký lớp học',
                                default => $state ?? '',
                            })
                            ->columnSpan(1),
                        TextEntry::make('semester.name')
                            ->label('Học kỳ')
                            ->columnSpan(1),
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'pending' => 'Đang chờ',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối',
                                default => $state ?? '',
                            })
                            ->columnSpan(1),
                        TextEntry::make('fromClass.subject.name')
                            ->label('Lớp học muốn hủy/đổi')
                            ->description(fn (?ClassAdjustmentRequest $record): ?string =>
                                $record?->fromClass ? "Mã lớp: {$record->fromClass->code}" : null
                            )
                            ->visible(fn (?ClassAdjustmentRequest $record): bool =>
                                $record?->type !== 'register'
                            )
                            ->columnSpan(1),
                        TextEntry::make('toClass.subject.name')
                            ->label('Lớp học muốn đăng ký')
                            ->description(fn (?ClassAdjustmentRequest $record): ?string =>
                                $record?->toClass ? "Mã lớp: {$record->toClass->code}" : null
                            )
                            ->visible(fn (?ClassAdjustmentRequest $record): bool =>
                                $record?->type !== 'cancel'
                            )
                            ->columnSpan(2),
                        TextEntry::make('reason')
                            ->label('Lý do')
                            ->columnSpan(3),
                        TextEntry::make('admin_note')
                            ->label('Ghi chú của admin')
                            ->visible(fn (?ClassAdjustmentRequest $record): bool => !empty($record?->admin_note))
                            ->columnSpan(3),
                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i')
                            ->columnSpan(3),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassAdjustmentRequests::route('/'),
            'create' => Pages\CreateClassAdjustmentRequest::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('student_id', auth()->user()?->student?->id)
            ->with(['fromClass.subject', 'toClass.subject', 'semester']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->student !== null;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
