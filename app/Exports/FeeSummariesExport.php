<?php

namespace App\Exports;

use App\Models\Batch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FeeSummariesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $batchId;

    public function __construct($batchId = null)
    {
        $this->batchId = $batchId;
    }

    public function collection()
    {
        $query = Batch::withCount(['students', 'fees'])
            ->withSum('fees', 'amount_paid');

        // Apply batch filter if provided
        if ($this->batchId && is_numeric($this->batchId)) {
            $query->where('id', (int) $this->batchId);
        }

        $results = $query->orderBy('name')->get();
        
        // For debugging - you can check the logs to see what's being filtered
        \Log::info('FeeSummariesExport - BatchId: ' . $this->batchId . ', Results count: ' . $results->count());
        
        return $results;
    }

    public function headings(): array
    {
        return [
            'Batch Name',
            'Total Students',
            'Total Course Fees (₹)',
            'Total Fees Paid (₹)',
            'Balance Amount (₹)',
            'Payment Percentage (%)',
            'Students with Payments',
            'Description',
        ];
    }

    public function map($batch): array
    {
        return [
            $batch->name,
            $batch->students_count,
            number_format($batch->total_course_fees, 2),
            number_format($batch->total_fees_paid, 2),
            number_format($batch->balance_amount, 2),
            number_format($batch->payment_percentage, 1),
            $batch->students_with_payments,
            $batch->description ?? '',
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