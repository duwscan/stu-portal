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
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $query = $this->getFilteredTableQuery();
                    
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\OpenClassRequestExport($query),
                        'yeu-cau-mo-lop-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
                    );
                }),
            Actions\Action::make('export_detailed')
                ->label('Xuất Excel chi tiết')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function () {
                    $query = $this->getFilteredTableQuery();
                    
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\OpenClassRequestDetailedExport($query),
                        'yeu-cau-mo-lop-chi-tiet-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
                    );
                }),
            // Actions\CreateAction::make(),
        ];
    }
}
