<?php

namespace App\Filament\Resources\FeeResource\Pages;

use App\Filament\Resources\FeeResource;
use App\Exports\FeePaymentsExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListFees extends ListRecords
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export')
                ->label('Export Fee Payments')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $filename = 'fee_payments_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                    return Excel::download(new FeePaymentsExport(), $filename);
                }),
        ];
    }



    protected function getFooterWidgets(): array
    {
        return [];
    }
}
