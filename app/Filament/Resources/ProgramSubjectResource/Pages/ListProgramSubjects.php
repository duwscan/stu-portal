<?php

namespace App\Filament\Resources\ProgramSubjectResource\Pages;

use App\Filament\Resources\ProgramSubjectResource;
use App\Imports\SubjectImport;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;

class ListProgramSubjects extends ListRecords
{
    protected static string $resource = ProgramSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
                ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                    return $collection;
                })
                ->use(SubjectImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
