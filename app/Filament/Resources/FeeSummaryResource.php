<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeSummaryResource\Pages;
use App\Models\Batch;
use App\Models\Fee;
use App\Models\User;
use App\Exports\FeeSummariesExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class FeeSummaryResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?string $modelLabel = 'Fee Summary';

    protected static ?string $pluralModelLabel = 'Fee Summaries';

    protected static ?string $slug = 'fee-summary';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount(['students', 'fees'])->withSum('fees', 'amount_paid');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // This resource is primarily for viewing summaries, so form is minimal
                Forms\Components\Section::make('Batch Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Batch Name')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
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
                Tables\Filters\SelectFilter::make('batch_id')
                    ->label('Batch')
                    ->options(function () {
                        return Batch::pluck('name', 'id')->toArray();
                    })
                    ->placeholder('All Batches')
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->where('id', $data['value']);
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export_filtered')
                        ->label('Export Filtered Fee Summaries')
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
                                $batchName = Batch::find($batchId)?->name ?? 'unknown';
                                $filename = 'fee_summaries_batch_' . $batchName . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                            }
                            
                            return Excel::download(new FeeSummariesExport($batchId), $filename);
                        }),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeSummaries::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Temporarily hidden - Show for admin, accounts, and manager users
        return false; // auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isAccounts() || auth()->user()->isManager());
    }

} 