<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Exports\StudentsExport;
use App\Models\Batch;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_all')
                ->label('Export All Students')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(new StudentsExport(), 'all_students_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
                }),
            Actions\Action::make('export_by_batch')
                ->label('Export by Batch')
                ->icon('heroicon-o-funnel')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('batch_id')
                        ->label('Select Batch')
                        ->options(Batch::pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('Select a batch to export')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $batchId = $data['batch_id'];
                    $batch = Batch::find($batchId);
                    $batchName = $batch ? str_replace(' ', '_', $batch->name) : 'batch_' . $batchId;
                    
                    return Excel::download(
                        new StudentsExport($batchId), 
                        $batchName . '_students_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                    );
                }),
            Actions\Action::make('export_filtered')
                ->label('Export Filtered Students')
                ->icon('heroicon-o-funnel')
                ->color('info')
                ->action(function () {
                    // Get the current table filters from the page
                    $tableFilters = $this->getTableFilters();
                    $batchFilter = $tableFilters['batch_id'] ?? null;
                    $batchId = $batchFilter ? $batchFilter->getState() : null;
                    
                    $filename = $batchId ? 'batch_' . $batchId . '_students_' . now()->format('Y-m-d_H-i-s') . '.xlsx' : 'filtered_students_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                    
                    return Excel::download(new StudentsExport($batchId), $filename);
                })
                ->visible(fn () => !empty($this->getTableFilters())),
            Actions\CreateAction::make(),
        ];
    }
} 