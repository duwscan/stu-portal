<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function afterCreate(): void
    {
        try {
            $this->record->assignRole('student');
            \Log::info('Assigned student role to user: ' . $this->record->id);
        } catch (\Exception $e) {
            \Log::error('Failed to assign student role: ' . $e->getMessage());
            throw $e;
        }
    }
}
