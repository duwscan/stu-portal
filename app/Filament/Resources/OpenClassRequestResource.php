<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\OpenClassRequestResource\Pages;
use App\Models\OpenClassRequest;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OpenClassRequestResource extends Resource
{
    protected static ?string $model = OpenClassRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Yêu cầu mở lớp';

    protected static ?string $modelLabel = 'Yêu cầu mở lớp';

    protected static ?string $pluralModelLabel = 'Yêu cầu mở lớp';

    protected static ?string $navigationGroup = 'Quản lý yêu cầu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Môn học')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'name')
                    ->label('Sinh viên')
                    ->required()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->student_id})"),
                Forms\Components\Select::make('semester_id')
                    ->relationship('semester', 'name')
                    ->label('Học kỳ')
                    ->required()
                    ->default(fn() => Semester::getCurrentSemester()?->id),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'approved' => 'Đã duyệt',
                        'rejected' => 'Từ chối',
                    ])
                    ->label('Trạng thái')
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('note')
                    ->label('Ghi chú của sinh viên')
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
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Môn học')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('semester.name')
                    ->label('Học kỳ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Số sinh viên tham gia')
                    ->counts('students')
                    ->sortable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Ghi chú')
                    ->limit(50)
                    ->tooltip(fn(OpenClassRequest $record): string => $record->note ?? ''),
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
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Học kỳ')
                    ->relationship('semester', 'name')
                    ->default(Semester::getCurrentSemester()?->id),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết'),
                Tables\Actions\Action::make('export')
                    ->label('Xuất Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (OpenClassRequest $record) {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\SingleOpenClassRequestExport($record),
                            'yeu-cau-mo-lop-' . $record->subject?->code . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                        );
                    }),
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
            'index' => Pages\ListOpenClassRequests::route('/'),
            'create' => Pages\CreateOpenClassRequest::route('/create'),
            'view' => Pages\ViewOpenClassRequest::route('/{record}'),
            'edit' => Pages\EditOpenClassRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['subject', 'student', 'semester', 'students'])
            ->whereHas('semester', function (Builder $query) {
                $query->where('id', Semester::getCurrentSemester()?->id);
            });
    }
}
