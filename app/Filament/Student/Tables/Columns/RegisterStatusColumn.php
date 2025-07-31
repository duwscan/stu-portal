<?php

declare(strict_types=1);

namespace App\Filament\Student\Tables\Columns;

use App\Models\ClassRoom;
use Filament\Tables\Columns\Column;

class RegisterStatusColumn extends Column
{
    protected string $view = 'filament.tables.columns.register-status-column';

    public function getState(): mixed
    {
        $record = $this->getRecord();

        if (!$record instanceof ClassRoom) {
            return [];
        }

        $status = $record->getRegisterStatus(auth()->user());

        return [
            'canRegister' => $status->canRegister ?? false,
            'description' => $status->description ?? '',
        ];
    }

    public function getStateUsing(mixed $callback): static
    {
        return parent::getStateUsing($callback);
    }
}
