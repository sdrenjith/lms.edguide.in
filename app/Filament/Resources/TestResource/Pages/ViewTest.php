<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTest extends ViewRecord
{
    protected static string $resource = TestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_submissions')
                ->label('View Submissions')
                ->icon('heroicon-o-users')
                ->url(fn (): string => route('filament.admin.resources.tests.submissions', $this->record))
                ->color('success'),
            Actions\EditAction::make()
                ->label('Edit Test'),
        ];
    }
} 