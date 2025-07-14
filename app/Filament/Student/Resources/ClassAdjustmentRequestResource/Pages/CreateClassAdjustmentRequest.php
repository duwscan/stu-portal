<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\ClassAdjustmentRequestResource\Pages;

use App\Filament\Student\Resources\ClassAdjustmentRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassAdjustmentRequest extends CreateRecord
{
    protected static string $resource = ClassAdjustmentRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['student_id'] = auth()->user()?->student?->id;

        return $data;
    }
}
