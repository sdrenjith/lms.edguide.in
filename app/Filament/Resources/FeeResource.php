<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeResource\Pages;
use App\Models\Batch;
use App\Models\Fee;
use App\Models\User;
use App\Exports\FeePaymentsExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class FeeResource extends Resource
{
    protected static ?string $model = Fee::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-rupee';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?string $modelLabel = 'Fee Payment';

    protected static ?string $pluralModelLabel = 'Fee Payments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\Select::make('batch_id')
                            ->label('Batch')
                            ->options(Batch::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Clear student selection when batch changes
                                $set('student_id', null);
                            }),
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->options(function (callable $get) {
                                $batchId = $get('batch_id');
                                if (!$batchId) {
                                    return [];
                                }
                                return User::where('role', 'student')
                                    ->where('batch_id', $batchId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->disabled(fn (callable $get) => !$get('batch_id')),
                        Forms\Components\TextInput::make('amount_paid')
                            ->label('Amount Paid (₹)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Payment Date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'cheque' => 'Cheque',
                                'online' => 'Online Payment',
                                'upi' => 'UPI',
                                'card' => 'Card',
                            ])
                            ->default('cash')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Any additional notes about this payment'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('batch.name')
                    ->label('Batch')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Amount Paid')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'bank_transfer' => 'info',
                        'cheque' => 'warning',
                        'online' => 'primary',
                        'upi' => 'danger',
                        'card' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recorded On')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('batch_id')
                    ->label('Batch')
                    ->options(Batch::pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('student_id')
                    ->label('Student')
                    ->options(User::where('role', 'student')->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\Filter::make('payment_date_range')
                    ->label('Payment Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'cheque' => 'Cheque',
                        'online' => 'Online Payment',
                        'upi' => 'UPI',
                        'card' => 'Card',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => !auth()->user()->isManager()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => !auth()->user()->isManager()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => !auth()->user()->isManager()),
                    Tables\Actions\BulkAction::make('export_filtered')
                        ->label('Export Filtered Fee Payments')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function () {
                            // Get the current table filters from the page
                            $tableFilters = $this->getTableFilters();
                            $fromDate = $tableFilters['payment_date_range']['from_date'] ?? null;
                            $toDate = $tableFilters['payment_date_range']['to_date'] ?? null;
                            $batchId = $tableFilters['batch_id'] ?? null;
                            
                            $filename = 'fee_payments_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                            if ($fromDate || $toDate) {
                                $filename = 'fee_payments_' . ($fromDate ?? 'all') . '_to_' . ($toDate ?? 'all') . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                            }
                            
                            return Excel::download(new FeePaymentsExport($fromDate, $toDate, $batchId), $filename);
                        }),
                ]),
            ])
            ->defaultSort('payment_date', 'desc')
            ->emptyStateHeading('No fee payments recorded')
            ->emptyStateDescription('Start by adding fee payments for your students.')
            ->emptyStateIcon('heroicon-o-currency-rupee')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Add Fee Payment')
                    ->url(route('filament.admin.resources.fees.create'))
                    ->icon('heroicon-m-plus')
                    ->button()
                    ->visible(fn () => !auth()->user()->isManager()),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        if (auth()->check() && auth()->user()->isManager()) {
            // For manager users, only show list page (read-only)
            return [
                'index' => Pages\ListFees::route('/'),
            ];
        }
        
        return [
            'index' => Pages\ListFees::route('/'),
            'create' => Pages\CreateFee::route('/create'),
            'edit' => Pages\EditFee::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['student', 'batch']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Temporarily hidden - Show for admin, accounts, and manager users
        return false; // auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isAccounts() || auth()->user()->isManager());
    }

    public static function canCreate(): bool
    {
        // Managers cannot create fee payments
        return !auth()->user()->isManager();
    }
}
