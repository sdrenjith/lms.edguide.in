<?php

namespace App\Filament\Resources\BatchResource\Pages;

use App\Filament\Resources\BatchResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;

class BatchStatus extends Page
{
    protected static string $resource = BatchResource::class;

    protected static string $view = 'filament.resources.batch-resource.pages.batch-status';

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher() || auth()->user()->isAccounts() || auth()->user()->isManager());
    }

    public ?array $data = [];

    public $record;

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->form->fill($this->getFormData());
    }

    protected function resolveRecord($record)
    {
        return \App\Models\Batch::findOrFail($record);
    }

    protected function getFormData(): array
    {
        $data = [];
        
        // Course assignments - get all courses from database
        $courses = \App\Models\Course::all();
        foreach ($courses as $course) {
            $data["course_{$course->id}"] = $this->record->courses->contains($course->id);
        }
        
        // Subject assignments - get all subjects from database
        $subjects = \App\Models\Subject::all();
        foreach ($subjects as $subject) {
            $data["subject_{$subject->id}"] = $this->record->subjects->contains($subject->id);
        }
        
        return $data;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Course Assignments')
                    ->description('Courses that students in this batch have access to')
                    ->schema([
                        Grid::make(2)
                            ->schema(function () {
                                // Get all courses from database
                                $courses = \App\Models\Course::all();
                                
                                return $courses->map(function ($course) {
                                    return Toggle::make("course_{$course->id}")
                                        ->label($course->name)
                                        ->disabled()
                                        ->extraAttributes(['class' => 'custom-green-toggle']);
                                })->toArray();
                            })
                    ])
                    ->columnSpanFull(),
                Section::make('Subject Assignments')
                    ->description('Subjects that are assigned to this batch')
                    ->schema([
                        Grid::make(2)
                            ->schema(function () {
                                // Get subjects based on user role
                                if (auth()->user()->isTeacher()) {
                                    // For teachers: only show their assigned subjects
                                    $subjects = auth()->user()->subjects;
                                } else {
                                    // For admins: show all subjects
                                    $subjects = \App\Models\Subject::all();
                                }
                                
                                return $subjects->map(function ($subject) {
                                    return Toggle::make("subject_{$subject->id}")
                                        ->label($subject->name)
                                        ->disabled()
                                        ->extraAttributes(['class' => 'custom-green-toggle']);
                                })->toArray();
                            })
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function getTitle(): string
    {
        return "Batch Status: {$this->record->name}";
    }

    public function getSubheading(): string
    {
        return 'View batch course assignments and day completion status';
    }
} 