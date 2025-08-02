<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Imports\GradeImport;
use App\Imports\StudentImport;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Collection;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
            ->processCollectionUsing(function (string $modelClass, Collection $collection) {
                return $collection;
            })
            ->use(StudentImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
