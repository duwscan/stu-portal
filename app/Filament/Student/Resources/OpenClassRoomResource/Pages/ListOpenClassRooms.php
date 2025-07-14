<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\OpenClassRoomResource\Pages;

use App\Filament\Student\Resources\OpenClassRoomResource;
use Filament\Resources\Pages\ListRecords;

class ListOpenClassRooms extends ListRecords
{
    protected static string $resource = OpenClassRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
