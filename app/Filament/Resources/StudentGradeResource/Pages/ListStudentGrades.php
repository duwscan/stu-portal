<?php

declare(strict_types=1);

namespace App\Filament\Resources\StudentGradeResource\Pages;

use App\Filament\Resources\StudentGradeResource;
use App\Imports\GradeImport;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;

class ListStudentGrades extends ListRecords
{
    protected static string $resource = StudentGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
                        ExcelImportAction::make()
            ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                return $collection;
            })
            ->modelLabel('Điểm')
            ->label('Import điểm')
            ->use(GradeImport::class),
        ];
    }
}
