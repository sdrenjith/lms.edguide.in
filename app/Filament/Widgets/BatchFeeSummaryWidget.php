<?php

namespace App\Filament\Widgets;

use App\Models\Batch;
use App\Models\Fee;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class BatchFeeSummaryWidget extends BaseWidget
{
    protected static ?string $heading = 'Batch-wise Fee Summary';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Batch::query()
                    ->withCount(['students', 'fees'])
                    ->withSum('fees', 'amount_paid')
            )
            ->emptyStateHeading('No batches available')
            ->emptyStateDescription('Create batches and assign students to see fee summaries here.')
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Batch Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Total Students')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_course_fees')
                    ->label('Total Course Fees')
                    ->money('INR')
                    ->getStateUsing(function ($record) {
                        return $record->total_course_fees;
                    })
                    ->description(function ($record) {
                        $studentCount = $record->students()->count();
                        return "From {$studentCount} students";
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_fees_paid')
                    ->label('Total Fees Paid')
                    ->money('INR')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->total_fees_paid;
                    })
                    ->description(function ($record) {
                        $studentsWithPayments = $record->students_with_payments;
                        $totalStudents = $record->students()->count();
                        return "From {$studentsWithPayments} out of {$totalStudents} students";
                    }),
                Tables\Columns\TextColumn::make('balance_amount')
                    ->label('Balance Amount')
                    ->money('INR')
                    ->getStateUsing(function ($record) {
                        return $record->balance_amount;
                    })
                    ->color(function ($record) {
                        return $record->balance_amount > 0 ? 'danger' : 'success';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_percentage')
                    ->label('Payment %')
                    ->getStateUsing(function ($record) {
                        return $record->payment_percentage;
                    })
                    ->suffix('%')
                    ->color(function ($record) {
                        $percentage = $record->payment_percentage;
                        if ($percentage >= 80) return 'success';
                        if ($percentage >= 50) return 'warning';
                        return 'danger';
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('has_students')
                    ->label('Has Students')
                    ->options([
                        'yes' => 'With Students',
                        'no' => 'Without Students',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === 'yes') {
                            return $query->has('students');
                        }
                        if ($data['value'] === 'no') {
                            return $query->doesntHave('students');
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->url(function (Batch $record): string {
                        return route('filament.admin.resources.fees.index', ['tableFilters[batch_id][value]' => $record->id]);
                    })
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('name');
    }
} 