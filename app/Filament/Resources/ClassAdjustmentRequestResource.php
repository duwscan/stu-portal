<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ClassAdjustmentRequestResource\Pages;
use App\Models\ClassAdjustmentRequest;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassAdjustmentRequestResource extends Resource
{
    protected static ?string $model = ClassAdjustmentRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Yêu cầu điều chỉnh lớp';

    protected static ?string $modelLabel = 'Yêu cầu điều chỉnh lớp';

    protected static ?string $pluralModelLabel = 'Yêu cầu điều chỉnh lớp';

    protected static ?string $navigationGroup = 'Quản lý yêu cầu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'name')
                    ->label('Sinh viên')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('semester_id')
                    ->relationship('semester', 'name')
                    ->label('Học kỳ')
                    ->required()
                    ->default(fn() => Semester::getCurrentSemester()?->id),
                Forms\Components\Select::make('from_class_id')
                    ->relationship('fromClass', 'code', function (Builder $query) {
                        return $query->with('subject');
                    })
                    ->label('Từ lớp')
                    ->required()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->code} - {$record->subject->name}"),
                Forms\Components\Select::make('to_class_id')
                    ->relationship('toClass', 'code', function (Builder $query) {
                        return $query->with('subject');
                    })
                    ->label('Đến lớp')
                    ->required()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->code} - {$record->subject->name}"),
                Forms\Components\Select::make('type')
                    ->options([
                        'change_class' => 'Chuyển lớp',
                        'add_class' => 'Thêm lớp',
                        'drop_class' => 'Bỏ lớp',
                    ])
                    ->label('Loại yêu cầu')
                    ->required()
                    ->default('change_class')
                    ->native(false),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                    ])
                    ->label('Trạng thái')
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('reason')
                    ->label('Lý do')
                    ->required()
                    ->rows(3),
                Forms\Components\Textarea::make('admin_note')
                    ->label('Ghi chú của admin')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_name')
                    ->label('Sinh viên')
                    ->searchable()
                    ->sortable()
                    ->state(function (ClassAdjustmentRequest $record): string {
                        return $record->student?->user?->name ?? 'Không tìm thấy sinh viên';
                    })
                    ->description(fn(ClassAdjustmentRequest $record): string => $record->student?->student_code ?? ''),
                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Học kỳ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fromClass.code')
                    ->label('Từ lớp')
                    ->description(fn(ClassAdjustmentRequest $record): string =>
                        $record->fromClass?->subject?->name ? "{$record->fromClass->subject->name} ({$record->fromClass->code})" : ''
                    )
                    ->searchable(),
                Tables\Columns\TextColumn::make('toClass.code')
                    ->label('Đến lớp')
                    ->description(fn(ClassAdjustmentRequest $record): string =>
                        $record->toClass?->subject?->name ? "{$record->toClass->subject->name} ({$record->toClass->code})" : ''
                    )
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Loại yêu cầu')
                    ->colors([
                        'info' => 'change_class',
                        'success' => 'add_class',
                        'warning' => 'drop_class',
                    ])
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'cancel' => 'Hủy lớp học',
                                'change' => 'Đổi lớp học',
                                'register' => 'Đăng ký lớp học',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn(string $state): string => match($state) {
                        'pending' => 'Chờ xử lý',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Lý do')
                    ->limit(50)
                    ->tooltip(fn(ClassAdjustmentRequest $record): string => $record->reason ?? ''),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại yêu cầu')
                    ->options([
                        'cancel' => 'Hủy lớp học',
                        'change' => 'Đổi lớp học',
                        'register' => 'Đăng ký lớp học',
                    ])
                    ->native(false),
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Học kỳ')
                    ->relationship('semester', 'name')
                    ->default(Semester::getCurrentSemester()?->id),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListClassAdjustmentRequests::route('/'),
            'create' => Pages\CreateClassAdjustmentRequest::route('/create'),
            'view' => Pages\ViewClassAdjustmentRequest::route('/{record}'),
            'edit' => Pages\EditClassAdjustmentRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['student', 'semester', 'student.user'])
            ->whereHas('semester', function (Builder $query) {
                $query->where('id', Semester::getCurrentSemester()?->id);
            });
    }
}
