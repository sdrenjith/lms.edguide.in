<?php

namespace App\Filament\Resources\TestResource\Pages;

use App\Filament\Resources\TestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTests extends ListRecords
{
    protected static string $resource = TestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create New Test'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();
        
        if ($user->isTeacher()) {
            return parent::getTableQuery()
                ->whereHas('subject', function (Builder $query) use ($user) {
                    $query->where('teacher_id', $user->id);
                });
        }
        
        return parent::getTableQuery();
    }
} 