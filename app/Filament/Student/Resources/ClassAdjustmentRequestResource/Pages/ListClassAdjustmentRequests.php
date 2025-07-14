<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\ClassAdjustmentRequestResource\Pages;

use App\Filament\Student\Resources\ClassAdjustmentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassAdjustmentRequests extends ListRecords
{
    protected static string $resource = ClassAdjustmentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
