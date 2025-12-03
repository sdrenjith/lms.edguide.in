<?php

namespace App\Filament\Resources\OpinionVerificationResource\Pages;

use App\Filament\Resources\OpinionVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditOpinionVerification extends EditRecord
{
    protected static string $resource = OpinionVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Only update verified_by and verified_at if user is updating the comment
        if (!empty($data['verification_comment']) && $this->getRecord()->verified_by === null) {
            $data['verified_by'] = auth()->id();
            $data['verified_at'] = now();
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Comment saved')
            ->body('Verification comment has been updated')
            ->success()
            ->send();
    }
} 