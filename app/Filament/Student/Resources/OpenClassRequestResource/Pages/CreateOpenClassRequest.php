<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\OpenClassRequestResource\Pages;

use App\Filament\Student\Resources\OpenClassRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOpenClassRequest extends CreateRecord
{
    protected static string $resource = OpenClassRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['student_id'] = auth()->user()?->student?->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Tự động thêm người tạo vào danh sách sinh viên tham gia
        $this->record->students()->attach($this->record->student_id);
    }
}
