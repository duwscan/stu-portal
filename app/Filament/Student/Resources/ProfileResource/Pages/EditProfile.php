<?php

declare(strict_types=1);

namespace App\Filament\Student\Resources\ProfileResource\Pages;

use App\Filament\Student\Resources\ProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Cập nhật hồ sơ cá nhân';

    public function mount(int | string $record = null): void
    {
        // Always edit the current user's profile
        $this->record = Auth::user();
        
        $this->fillForm();
        
        $this->previousUrl = url()->previous();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Hồ sơ đã được cập nhật thành công!';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove password if it's empty
        if (empty($data['password'])) {
            unset($data['password']);
        }
        
        // Remove password_confirmation as it's not a database field
        unset($data['password_confirmation']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Refresh the user instance to reflect any changes
        Auth::user()->refresh();
    }
}