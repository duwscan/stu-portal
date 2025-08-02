<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        try {
            if (!$this->record->hasRole('student')) {
                $this->record->assignRole('student');
                \Log::info('Assigned student role to user during update: ' . $this->record->id);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to assign student role during update: ' . $e->getMessage());
            throw $e;
        }
    }
}
