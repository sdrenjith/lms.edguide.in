<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('Question Bank');
    }

    public function getSubheading(): ?string
    {
        $totalQuestions = \App\Models\Question::count();
        $activeQuestions = \App\Models\Question::where('is_active', true)->count();
        
        return "Manage your assessment questions • {$totalQuestions} total questions • {$activeQuestions} active";
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Question')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->size('lg')
                ->outlined()
                ->extraAttributes([
                    'class' => 'shadow-sm hover:shadow-md transition-all duration-200'
                ]),
        ];
    }

    // Custom breadcrumbs
    public function getBreadcrumbs(): array
    {
        return [
            '/admin' => __('Dashboard'),
            '' => __('Questions'),
        ];
    }
}