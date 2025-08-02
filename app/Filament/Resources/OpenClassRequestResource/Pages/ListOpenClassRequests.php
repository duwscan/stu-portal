<?php

declare(strict_types=1);

namespace App\Filament\Resources\OpenClassRequestResource\Pages;

use App\Exports\OpenClassRequestExport;
use App\Filament\Resources\OpenClassRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListOpenClassRequests extends ListRecords
{
    protected static string $resource = OpenClassRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Xuất Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    return Excel::download(new OpenClassRequestExport(), 'yeu-cau-mo-lop-' . now()->format('Y-m-d_H-i-s') . '.xlsx');
                }),
            // Actions\CreateAction::make(),
        ];
    }
}
