<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditTest extends EditRecord
{
    protected static string $resource = TestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Delete Test'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Test updated successfully!')
            ->body('The test has been updated.')
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure passmark doesn't exceed total_score
        if (isset($data['passmark']) && isset($data['total_score'])) {
            if ($data['passmark'] > $data['total_score']) {
                $data['passmark'] = $data['total_score'];
            }
        }

        return $data;
    }
} 