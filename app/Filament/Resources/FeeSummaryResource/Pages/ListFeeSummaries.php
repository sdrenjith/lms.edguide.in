<?php

namespace App\Filament\Resources\FeeSummaryResource\Pages;

use App\Filament\Resources\FeeSummaryResource;
use App\Exports\FeeSummariesExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListFeeSummaries extends ListRecords
{
    protected static string $resource = FeeSummaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Fee Summaries')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Get the current table filters using multiple methods
                    $batchId = null;
                    
                    // Method 1: Try request parameters
                    $batchId = request()->get('tableFilters.batch_id.value') ?? request()->get('tableFilters.batch_id');
                    
                    // Method 2: Try session data
                    if (!$batchId) {
                        $batchId = session()->get('tableFilters.batch_id.value') ?? session()->get('tableFilters.batch_id');
                    }
                    
                    // Method 3: Try URL parameters
                    if (!$batchId) {
                        $urlParams = request()->query();
                        if (isset($urlParams['tableFilters']['batch_id']['value'])) {
                            $batchId = $urlParams['tableFilters']['batch_id']['value'];
                        } elseif (isset($urlParams['tableFilters']['batch_id'])) {
                            $batchId = $urlParams['tableFilters']['batch_id'];
                        }
                    }
                    
                    $filename = 'fee_summaries_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                    if ($batchId) {
                        $batchName = \App\Models\Batch::find($batchId)?->name ?? 'unknown';
                        $filename = 'fee_summaries_batch_' . $batchName . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                    }
                    
                    return Excel::download(new FeeSummariesExport($batchId), $filename);
                }),
        ];
    }


} 