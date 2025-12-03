<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\Page;

class StudentProgress extends Page
{
    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.student-progress';

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher() || auth()->user()->isAccounts() || auth()->user()->isManager());
    }

    public $record;

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function resolveRecord($record)
    {
        return \App\Models\User::findOrFail($record);
    }

    public function getTitle(): string
    {
        return "Progress Report: {$this->record->name}";
    }

    public function getSubheading(): string
    {
        return 'View student progress across all subjects and courses';
    }
}