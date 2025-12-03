<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTest extends CreateRecord
{
    protected static string $resource = TestResource::class;

    public function getCreateAnotherAction(): ?\Filament\Actions\Action
    {
        return null;
    }

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();
        return array_filter($actions, function ($action) {
            return $action->getName() !== 'createAnother';
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Test created successfully!')
            ->body('The test has been created and is ready for use.')
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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