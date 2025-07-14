<?php

declare(strict_types=1);

namespace App\Filament\Student\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.student.pages.dashboard';

    public function getTitle(): string
    {
        return 'Trang chủ sinh viên';
    }
}
