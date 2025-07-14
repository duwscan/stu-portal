<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\StudentSubjectResource\Pages;

use App\Filament\Student\Resources\StudentSubjectResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentSubjects extends ListRecords
{
    protected static string $resource = StudentSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
