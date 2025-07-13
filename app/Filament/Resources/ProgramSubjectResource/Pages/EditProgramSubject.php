<?php

namespace App\Filament\Resources\ProgramSubjectResource\Pages;

use App\Filament\Resources\ProgramSubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramSubject extends EditRecord
{
    protected static string $resource = ProgramSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
