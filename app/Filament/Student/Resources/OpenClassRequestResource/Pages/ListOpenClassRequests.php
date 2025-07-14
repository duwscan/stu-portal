<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\OpenClassRequestResource\Pages;

use App\Filament\Student\Resources\OpenClassRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOpenClassRequests extends ListRecords
{
    protected static string $resource = OpenClassRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
