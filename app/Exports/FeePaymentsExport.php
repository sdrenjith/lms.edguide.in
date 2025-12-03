<?php

namespace App\Exports;

use App\Models\Fee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class FeePaymentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $fromDate;
    protected $toDate;
    protected $batchId;

    public function __construct($fromDate = null, $toDate = null, $batchId = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->batchId = $batchId;
    }

    public function collection()
    {
        $query = Fee::with(['student', 'batch']);

        // Apply date filters if provided
        if ($this->fromDate) {
            $query->whereDate('payment_date', '>=', $this->fromDate);
        }

        if ($this->toDate) {
            $query->whereDate('payment_date', '<=', $this->toDate);
        }

        // Apply batch filter if provided
        if ($this->batchId) {
            $query->where('batch_id', $this->batchId);
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Batch',
            'Amount Paid (₹)',
            'Payment Date',
            'Payment Method',
            'Notes',
            'Recorded On',
        ];
    }

    public function map($fee): array
    {
        return [
            $fee->student ? $fee->student->name : 'Unknown Student',
            $fee->batch ? $fee->batch->name : 'No Batch',
            number_format($fee->amount_paid, 2),
            $fee->payment_date ? Carbon::parse($fee->payment_date)->format('d/m/Y') : '',
            ucfirst($fee->payment_method ?? ''),
            $fee->notes ?? '',
            $fee->created_at ? Carbon::parse($fee->created_at)->format('d/m/Y H:i:s') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ];
    }
} 