<?php

declare(strict_types=1);

namespace App\Filament\Student\Widgets;

use Filament\Widgets\AccountWidget;

class StudentAccountWidget extends AccountWidget
{
    protected int | string | array $columnSpan = 'full';
}
