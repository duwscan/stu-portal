<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\OpenClassRequestResource\Pages;
use App\Models\OpenClassRequest;
use App\Models\Semester;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Filament\Support\Markdown;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class OpenClassRequestResource extends Resource
{
    protected static ?string $model = OpenClassRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Yêu cầu mở lớp';

    protected static ?string $modelLabel = 'Yêu cầu mở lớp';

    protected static ?string $pluralModelLabel = 'Yêu cầu mở lớp';

    public static function form(Form $form): Form
    {
        $student = auth()->user()?->student;

        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->label('Môn học')
                    ->options(function () use ($student) {
                        if (!$student) {
                            return [];
                        }

                        // Lấy danh sách môn học trong chương trình đào tạo
                        $subjectIds = $student->trainingProgram->programSubjects()
                            ->where('is_active', true)
                            ->pluck('subject_id');

                        return Subject::whereIn('id', $subjectIds)
                            ->whereNull('deleted_at')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('semester_id')
                    ->label('Học kỳ')
                    ->options(fn () => Semester::query()->pluck('name', 'id'))
                    ->default(fn () => Semester::getCurrentSemester()?->id)
                    ->required(),
                Forms\Components\Textarea::make('note')
                    ->label('Ghi chú')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Tên môn học')
                    ->description(fn (OpenClassRequest $record): string => "Mã môn: {$record->subject->code}")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.credits')
                    ->label('Số tín chỉ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Học kỳ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Số sinh viên')
                    ->counts('students')
                    ->description(function (OpenClassRequest $record): ?string {
                        $student = auth()->user()?->student;
                        if (!$student) {
                            return null;
                        }
                        return $record->students()->where('student_id', $student->id)->exists()
                            ? 'Bạn đã tham gia'
                            : null;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Người tạo')
                    ->description(fn (OpenClassRequest $record): string =>
                        $record->student_id === auth()->user()?->student?->id ? '(Yêu cầu của bạn)' : ''
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Đang chờ',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        'cancelled' => 'Đã hủy',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Học kỳ')
                    ->options(fn () => Semester::query()->pluck('name', 'id'))
                    ->default(fn () => Semester::getCurrentSemester()?->id),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Đang chờ',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                        'cancelled' => 'Đã hủy',
                    ]),
                Tables\Filters\TernaryFilter::make('my_requests')
                    ->label('Yêu cầu của tôi')
                    ->queries(
                        true: fn (Builder $query) => $query->where('student_id', auth()->user()?->student?->id),
                        false: fn (Builder $query) => $query->where('student_id', '!=', auth()->user()?->student?->id),
                        blank: fn (Builder $query) => $query
                    ),
                Tables\Filters\TernaryFilter::make('joined')
                    ->label('Đã tham gia')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('students', fn ($q) =>
                            $q->where('student_id', auth()->user()?->student?->id)
                        ),
                        false: fn (Builder $query) => $query->whereDoesntHave('students', fn ($q) =>
                            $q->where('student_id', auth()->user()?->student?->id)
                        ),
                        blank: fn (Builder $query) => $query
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('join')
                    ->label('Tham gia')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tham gia yêu cầu mở lớp')
                    ->modalDescription(fn (OpenClassRequest $record) => "Bạn có chắc chắn muốn tham gia yêu cầu mở lớp môn {$record->subject->name}?")
                    ->visible(function (OpenClassRequest $record): bool {
                        $student = auth()->user()?->student;
                        if (!$student) {
                            return false;
                        }
                        [$canJoin] = $record->canStudentJoin($student);
                        return $canJoin;
                    })
                    ->action(function (OpenClassRequest $record) {
                        $student = auth()->user()?->student;
                        if (!$student) {
                            return;
                        }
                        [$canJoin, $message] = $record->canStudentJoin($student);
                        if (!$canJoin) {
                            return;
                        }
                        $record->students()->attach($student->id);
                    }),
                Tables\Actions\Action::make('leave')
                    ->label('Hủy tham gia')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hủy tham gia yêu cầu mở lớp')
                    ->modalDescription(fn (OpenClassRequest $record) => "Bạn có chắc chắn muốn hủy tham gia yêu cầu mở lớp môn {$record->subject->name}?")
                    ->visible(function (OpenClassRequest $record): bool {
                        $student = auth()->user()?->student;
                        if (!$student) {
                            return false;
                        }
                        return $record->status === 'pending' &&
                            $record->student_id !== $student->id && // Không phải người tạo
                            $record->students()->where('student_id', $student->id)->exists(); // Đã tham gia
                    })
                    ->action(function (OpenClassRequest $record) {
                        $student = auth()->user()?->student;
                        if (!$student) {
                            return;
                        }
                        $record->students()->detach($student->id);
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('Hủy yêu cầu')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hủy yêu cầu mở lớp')
                    ->modalDescription('Bạn có chắc chắn muốn hủy yêu cầu mở lớp này?')
                    ->visible(fn (OpenClassRequest $record): bool =>
                        $record->student_id === auth()->user()?->student?->id &&
                        $record->status === 'pending' &&
                        $record->students()->count() <= 1 // Chỉ cho phép hủy khi chỉ có mình người tạo tham gia
                    )
                    ->action(function (OpenClassRequest $record) {
                        $record->update(['status' => 'cancelled']);
                        // Hủy tham gia của người tạo
                        $record->students()->detach($record->student_id);
                    }),
                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết'),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin chi tiết')
                    ->schema([
                        TextEntry::make('subject.name')
                            ->label('Môn học')
                            ->columnSpan(1),
                        TextEntry::make('subject.credits')
                            ->label('Số tín chỉ')
                            ->columnSpan(1),
                        TextEntry::make('semester.name')
                            ->label('Học kỳ')
                            ->columnSpan(1),
                        TextEntry::make('student.user.name')
                            ->label('Người tạo')
                            ->columnSpan(1),
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Đang chờ',
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Từ chối',
                                'cancelled' => 'Đã hủy',
                                default => $state,
                            })
                            ->columnSpan(1),
                        TextEntry::make('students_count')
                            ->label('Số sinh viên đăng ký')
                            ->state(fn (OpenClassRequest $record): int => $record->students()->count())
                            ->columnSpan(1),
                        TextEntry::make('note')
                            ->label('Ghi chú')
                            ->visible(fn (OpenClassRequest $record): bool => !empty($record->note))
                            ->columnSpan(3),
                        TextEntry::make('admin_note')
                            ->label('Ghi chú của admin')
                            ->visible(fn (OpenClassRequest $record): bool => !empty($record->admin_note))
                            ->columnSpan(3),
                    ])
                    ->columns(3),
                Section::make('Danh sách sinh viên đăng ký')
                    ->schema([
                        TextEntry::make('students')
                            ->label('')
                            ->listWithLineBreaks()
                            ->formatStateUsing(fn (OpenClassRequest $record): array =>
                                $record->students()->with('user')->get()->map(fn ($student) => $student->user->name)->toArray()
                            )
                            ->columnSpan(3),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpenClassRequests::route('/'),
            'create' => Pages\CreateOpenClassRequest::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('students')
            ->with(['subject', 'semester', 'student']);
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
