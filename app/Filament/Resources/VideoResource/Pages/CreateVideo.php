<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use App\Helpers\VideoHelper;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateVideo extends CreateRecord
{
    protected static string $resource = VideoResource::class;
    public static bool $createAnother = false;

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();
        
        // Debug logging
        \Log::info('CreateVideo - Form data:', $data);
        
        // Validate that either YouTube URL or video file is provided
        if (empty($data['youtube_url']) && empty($data['video_path'])) {
            \Log::info('CreateVideo - Validation failed: No video or URL provided');
            $this->addError('youtube_url', 'Either a YouTube URL or video file must be provided.');
            $this->addError('video_path', 'Either a YouTube URL or video file must be provided.');
            $this->halt();
        }

        \Log::info('CreateVideo - Validation passed, proceeding with creation');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Handle file upload
        if (!empty($data['video_path'])) {
            // Clear YouTube URL if file is uploaded
            $data['youtube_url'] = null;
        }

        // Handle YouTube URL
        if (!empty($data['youtube_url'])) {
            // Clear video_path if YouTube URL is provided
            $data['video_path'] = null;
        }

        return $data;
    }

    protected function getFormActions(): array
    {
        $actions = array_filter(parent::getFormActions(), function ($action) {
            return $action->getName() !== 'createAnother';
        });
        
        return $actions;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Debug logging after creation
        \Log::info('CreateVideo - Record created:', $this->record->toArray());
    }
} 