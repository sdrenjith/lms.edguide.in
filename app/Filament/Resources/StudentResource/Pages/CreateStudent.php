<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'student';
        
        // If verification code is selected, assign student to the course and subject
        if (!empty($data['verification_code_id'])) {
            $verificationCode = \App\Models\VerificationCode::find($data['verification_code_id']);
            if ($verificationCode) {
                // Don't assign batch_id for verification code students
                $data['verification_code'] = $verificationCode->code;
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;
        
        // If verification code was selected and student is verified, mark code as used
        if (!empty($user->verification_code_id) && $user->is_verified) {
            $verificationCode = \App\Models\VerificationCode::find($user->verification_code_id);
            if ($verificationCode) {
                $verificationCode->markAsUsed($user);
            }
        }
    }

    protected function getFormActions(): array
    {
        $actions = parent::getFormActions();
        return array_filter($actions, function ($action) {
            return $action->getName() !== 'createAnother';
        });
    }
} 