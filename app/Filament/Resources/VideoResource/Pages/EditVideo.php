<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use App\Helpers\VideoHelper;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditVideo extends EditRecord
{
    protected static string $resource = VideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->modalHeading('Video Preview')
                ->modalContent(fn($record) => view('filament.resources.video-resource.preview', ['record' => $record]))
                ->color('info')
                ->modalSubmitActionLabel('Close')
                ->modalCancelAction(false),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle file upload
        if (!empty($data['replace_video'])) {
            $data['video_path'] = $data['replace_video'];
            // Clear YouTube URL if file is uploaded
            $data['youtube_url'] = null;
        }

        // Handle YouTube URL
        if (!empty($data['youtube_url'])) {
            if (VideoHelper::isValidYoutubeUrl($data['youtube_url'])) {
                // Clear video_path if YouTube URL is provided
                $data['video_path'] = null;
            } else {
                // Invalid YouTube URL, remove it
                $data['youtube_url'] = null;
            }
        }

        // Remove the replace_video field as it's not a database column
        unset($data['replace_video']);

        return $data;
    }
} 