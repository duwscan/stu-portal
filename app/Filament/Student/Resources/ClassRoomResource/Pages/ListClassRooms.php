<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\ClassRoomResource\Pages;

use App\Filament\Student\Resources\ClassRoomResource;
use Filament\Resources\Pages\ListRecords;

class ListClassRooms extends ListRecords
{
    protected static string $resource = ClassRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
