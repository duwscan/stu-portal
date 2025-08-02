<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Filament\Resources\SubjectResource;
use App\Imports\GradeImport;
use App\Imports\SubjectImport;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
