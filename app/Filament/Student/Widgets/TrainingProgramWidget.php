<?php

declare(strict_types=1);

namespace App\Filament\Student\Widgets;

use App\Models\Student;
use Filament\Widgets\Widget;

class TrainingProgramWidget extends Widget
{
    protected static string $view = 'filament.student.widgets.training-program-widget';

    // Thay đổi từ 'full' thành 'half' để hiển thị 2 cột
    protected int | string | array $columnSpan = 'half';

    public function getTrainingProgram()
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student || !$student->trainingProgram) {
            return null;
        }

        return $student->trainingProgram;
    }
}
