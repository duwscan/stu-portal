<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\FeedbackResource\Pages;

use App\Filament\Student\Resources\FeedbackResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeedback extends CreateRecord
{
    protected static string $resource = FeedbackResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}