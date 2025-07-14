<?php

declare(strict_types=1);

namespace App\Filament\Student\Widgets;

use App\Models\Semester;
use Filament\Widgets\Widget;

class CurrentSemesterWidget extends Widget
{
    protected static string $view = 'filament.student.widgets.current-semester-widget';

    // Thay đổi từ 'full' thành 'half' để hiển thị 2 cột
    protected int | string | array $columnSpan = 'half';

    public function getCurrentSemester()
    {
        return Semester::getCurrentSemester();
    }

    public function getStudentClasses()
    {
        $student = auth()->user()->student;
        $currentSemester = $this->getCurrentSemester();

        if (!$student || !$currentSemester) {
            return collect();
        }

        return collect();
    }
}
