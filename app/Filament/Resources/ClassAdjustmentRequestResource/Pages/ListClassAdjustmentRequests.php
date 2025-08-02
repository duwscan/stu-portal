<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClassAdjustmentRequestResource\Pages;

use App\Exports\ClassAdjustmentRequestExport;
use App\Filament\Resources\ClassAdjustmentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListClassAdjustmentRequests extends ListRecords
{
    protected static string $resource = ClassAdjustmentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Xuất Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    return Excel::download(new ClassAdjustmentRequestExport(), 'class-adjustment-requests.xlsx');
                }),
            // Actions\CreateAction::make(),
        ];
    }
}
