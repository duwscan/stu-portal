<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Cài đặt';

    protected static ?string $label = 'Môn học';
    protected static ?string $pluralLabel = 'Môn học';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin môn học')
                    ->description('Nhập thông tin chi tiết môn học')
                    ->icon('heroicon-o-book-open')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Mã môn học')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Ví dụ: COMP101')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('name')
                            ->label('Tên môn học')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ví dụ: Lập trình cơ bản')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('credits')
                            ->label('Số tín chỉ')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1)
                            ->maxValue(10)
                            ->step(1)
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Đang hoạt động')
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),
                        Forms\Components\RichEditor::make('description')
                            ->label('Mô tả môn học')
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
                    ->label('Mã môn học')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Đã sao chép mã môn học')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên môn học')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('credits')
                    ->label('Số tín chỉ')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái')
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
                        ->modalDescription('Bạn có chắc chắn muốn xóa các môn học đã chọn?'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm môn học'),
            ])
            ->emptyStateDescription('Chưa có môn học nào được tạo.')
            ->emptyStateHeading('Chưa có dữ liệu')
            ->emptyStateIcon('heroicon-o-book-open');
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
