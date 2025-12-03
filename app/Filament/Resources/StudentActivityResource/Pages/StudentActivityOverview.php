<?php

namespace App\Filament\Resources\StudentActivityResource\Pages;

use App\Filament\Resources\StudentActivityResource;
use App\Models\StudentActivity;
use App\Models\User;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class StudentActivityOverview extends ListRecords
{
    protected static string $resource = StudentActivityResource::class;

    protected static string $view = 'filament.resources.student-activity-resource.pages.student-activity-overview';

    protected static ?string $navigationLabel = 'Student Activity Overview';
    
    protected static ?string $title = 'Student Activity Overview';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('role', 'student')
                    ->with(['activities' => function ($query) {
                        $query->latest('login_at');
                    }])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('batch.name')
                    ->label('Batch')
                    ->sortable()
                    ->placeholder('No Batch'),
                TextColumn::make('last_login')
                    ->label('Last Login')
                    ->getStateUsing(function (User $record) {
                        $lastActivity = $record->activities()->latest('login_at')->first();
                        return $lastActivity ? $lastActivity->login_at->format('d M Y, h:i A') : 'Never';
                    })
                    ->sortable()
                    ->color(fn ($state) => $state === 'Never' ? 'gray' : 'success'),
                TextColumn::make('current_status')
                    ->label('Current Status')
                    ->getStateUsing(function (User $record) {
                        $activeSession = $record->activities()->whereNull('logout_at')->latest()->first();
                        return $activeSession ? 'Online' : 'Offline';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'Online' ? 'success' : 'gray'),
                TextColumn::make('total_sessions')
                    ->label('Total Sessions')
                    ->getStateUsing(fn (User $record) => $record->activities()->count())
                    ->sortable(),
                TextColumn::make('total_time')
                    ->label('Total Time')
                    ->getStateUsing(function (User $record) {
                        $totalMinutes = $record->activities()
                            ->whereNotNull('session_duration_minutes')
                            ->sum('session_duration_minutes');
                        return $totalMinutes > 0 ? round($totalMinutes / 60, 1) . ' hours' : '0 hours';
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('batch_id')
                    ->label('Batch')
                    ->relationship('batch', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'online' => 'Online',
                        'offline' => 'Offline',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === 'online') {
                            return $query->whereHas('activities', function ($q) {
                                $q->whereNull('logout_at');
                            });
                        } elseif ($data['value'] === 'offline') {
                            return $query->whereDoesntHave('activities', function ($q) {
                                $q->whereNull('logout_at');
                            });
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (User $record): string => route('filament.admin.resources.student-activities.index', [
                        'tableFilters' => [
                            'user_id' => [
                                'value' => $record->id
                            ]
                        ]
                    ])),
            ])
            ->defaultSort('last_login', 'desc');
    }
}
