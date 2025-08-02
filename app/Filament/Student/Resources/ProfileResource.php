<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\ProfileResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Hồ sơ cá nhân';

    protected static ?string $modelLabel = 'Hồ sơ cá nhân';

    protected static ?string $pluralModelLabel = 'Hồ sơ cá nhân';

    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin tài khoản')
                    ->description('Cập nhật thông tin tài khoản và mật khẩu')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Họ và tên')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Mật khẩu mới')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => !empty($state))
                            ->placeholder('Để trống nếu không muốn thay đổi')
                            ->helperText('Để trống nếu không muốn thay đổi mật khẩu'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Xác nhận mật khẩu')
                            ->password()
                            ->same('password')
                            ->requiredWith('password')
                            ->dehydrated(false)
                            ->placeholder('Nhập lại mật khẩu mới'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Thông tin cá nhân')
                    ->description('Cập nhật thông tin cá nhân')
                    ->relationship('student')
                    ->schema([
                        Forms\Components\TextInput::make('student_code')
                            ->label('Mã sinh viên')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Select::make('gender')
                            ->label('Giới tính')
                            ->options([
                                'Nam' => 'Nam',
                                'Nữ' => 'Nữ',
                                'Khác' => 'Khác',
                            ])
                            ->placeholder('Chọn giới tính'),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Ngày sinh')
                            ->maxDate(now()->subYears(16)),
                        Forms\Components\Textarea::make('address')
                            ->label('Địa chỉ')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('class')
                            ->label('Lớp')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('faculty')
                            ->label('Khoa')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\EditProfile::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('id', Auth::id());
    }
}