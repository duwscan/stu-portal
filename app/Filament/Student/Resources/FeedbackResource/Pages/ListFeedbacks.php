<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\FeedbackResource\Pages;

use App\Filament\Student\Resources\FeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeedbacks extends ListRecords
{
    protected static string $resource = FeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Gửi phản hồi mới'),
        ];
    }
}