<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Illuminate\Support\Collection;

class MultiSelectTeachers extends Field
{
    protected string $view = 'filament.forms.components.multi-select-teachers';

    protected int $visibleOptions = 8;

    public function visibleOptions(int $count): static
    {
        $this->visibleOptions = $count;
        return $this;
    }

    public function getVisibleOptions(): int
    {
        return $this->visibleOptions;
    }

    public function getTeachers(): Collection
    {
        return \App\Models\User::where('role', 'teacher')
            ->orderBy('name')
            ->get();
    }

    public function getSelectedValues(): array
    {
        $value = $this->getState();
        
        if (is_array($value)) {
            return $value;
        }
        
        if ($value instanceof Collection) {
            return $value->pluck('id')->toArray();
        }
        
        return [];
    }

    public function getState(): mixed
    {
        $state = parent::getState();
        
        if (is_null($state)) {
            return [];
        }
        
        return $state;
    }
}
