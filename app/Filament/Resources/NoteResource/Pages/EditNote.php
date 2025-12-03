<?php

namespace App\Filament\Resources\NoteResource\Pages;

use App\Filament\Resources\NoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNote extends EditRecord
{
    protected static string $resource = NoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->modalHeading('Note Preview')
                ->modalContent(fn($record) => view('filament.resources.note-resource.preview', ['record' => $record]))
                ->color('info')
                ->modalSubmitActionLabel('Close')
                ->modalCancelAction(false),
        ];
    }
} 