<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $batchId;
    protected $students;

    public function __construct($batchId = null, $students = null)
    {
        $this->batchId = $batchId;
        $this->students = $students;
    }

    public function collection()
    {
        if ($this->students) {
            return $this->students;
        }

        $query = User::where('role', 'student')->with('batch');

        if ($this->batchId) {
            $query->where('batch_id', $this->batchId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Username',
            'Email',
            'Phone',
            'Father\'s Name',
            'Mother\'s Name',
            'Date of Birth',
            'Gender',
            'Nationality',
            'Category',
            'Qualification',
            'Experience (Months)',
            'Passport Number',
            'Address',
            'Batch',
            'Course Fee (₹)',
            'Fees Paid (₹)',
            'Balance Due (₹)',
            'Father\'s WhatsApp',
            'Mother\'s WhatsApp'
        ];
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->name,
            $student->username,
            $student->email,
            $student->phone,
            $student->father_name,
            $student->mother_name,
            $student->dob ? $student->dob->format('d/m/Y') : '',
            $student->gender,
            $student->nationality,
            $student->category,
            $student->qualification,
            $student->experience_months,
            $student->passport_number,
            $student->address,
            $student->batch ? $student->batch->name : 'No Batch',
            $student->course_fee,
            $student->fees_paid,
            $student->balance_fees_due,
            $student->father_whatsapp,
            $student->mother_whatsapp
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ]
            ],
        ];
    }
} 