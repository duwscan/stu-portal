<?php

declare(strict_types=1);

namespace App\Filament\Resources\OpenClassRequestResource\Pages;

use App\Filament\Resources\OpenClassRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOpenClassRequest extends EditRecord
{
    protected static string $resource = OpenClassRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
