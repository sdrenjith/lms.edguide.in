<?php

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListDays extends ListRecords
{
    protected static string $resource = DayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Day')
                ->icon('heroicon-m-plus')
                ->tooltip('Create a new day for a course'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Days')
                ->icon('heroicon-m-calendar')
                ->badge(fn() => $this->getModel()::count()),
            
            'upcoming' => Tab::make('Upcoming')
                ->icon('heroicon-m-arrow-right')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date', '>=', Carbon::today()))
                ->badge(fn() => $this->getModel()::where('date', '>=', Carbon::today())->count()),
            
            'today' => Tab::make('Today')
                ->icon('heroicon-m-calendar-days')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('date', Carbon::today()))
                ->badge(fn() => $this->getModel()::whereDate('date', Carbon::today())->count()),
            
            'past' => Tab::make('Past')
                ->icon('heroicon-m-arrow-left')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('date', '<', Carbon::today()))
                ->badge(fn() => $this->getModel()::where('date', '<', Carbon::today())->count()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add custom widgets here if needed
        ];
    }
}