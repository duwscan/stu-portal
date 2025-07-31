<?php

declare(strict_types=1);

namespace App\Filament\Resources\OpenClassRequestResource\Pages;

use App\Filament\Resources\OpenClassRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOpenClassRequest extends CreateRecord
{
    protected static string $resource = OpenClassRequestResource::class;
}
