<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClassAdjustmentRequestResource\Pages;

use App\Filament\Resources\ClassAdjustmentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassAdjustmentRequest extends EditRecord
{
    protected static string $resource = ClassAdjustmentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
