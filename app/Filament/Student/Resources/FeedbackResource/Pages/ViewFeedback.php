<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\FeedbackResource\Pages;

use App\Filament\Student\Resources\FeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFeedback extends ViewRecord
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => $this->record->status === 'pending'),
        ];
    }
}