<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentActivityResource\Pages;
use App\Models\StudentActivity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class StudentActivityResource extends Resource
{
    protected static ?string $model = StudentActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = 'Student Activity';
    
    protected static ?string $navigationGroup = 'Students';
    
    protected static ?int $navigationSort = 4;
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isManager());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Student')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DateTimePicker::make('login_at')
                            ->label('Login Time')
                            ->required(),
                        Forms\Components\DateTimePicker::make('logout_at')
                            ->label('Logout Time'),
                        Forms\Components\DateTimePicker::make('last_activity_at')
                            ->label('Last Activity'),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address'),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->rows(3),
                        Forms\Components\Select::make('logout_type')
                            ->label('Logout Type')
                            ->options([
                                'manual' => 'Manual Logout',
                                'auto' => 'Automatic Logout',
                                'timeout' => 'Session Timeout',
                            ])
                            ->default('manual'),
                        Forms\Components\TextInput::make('session_duration_minutes')
                            ->label('Session Duration (minutes)')
                            ->numeric()
                            ->disabled(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student')
                    ->sortable()
                    ->searchable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('login_at')
                    ->label('Login Time')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('logout_at')
                    ->label('Logout Time')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->placeholder('Still Active')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Last Activity')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->placeholder('No Activity')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('session_duration_minutes')
                    ->label('Duration')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) {
                            return $state . ' min';
                        }
                        
                        // Calculate current session duration if still active
                        if (!$record->logout_at && $record->login_at) {
                            $duration = abs(now()->diffInMinutes($record->login_at));
                            return $duration . ' min (active)';
                        }
                        
                        return 'N/A';
                    })
                    ->color(fn ($state, $record) => $record->logout_at ? 'success' : 'warning'),
                Tables\Columns\BadgeColumn::make('logout_type')
                    ->label('Logout Type')
                    ->colors([
                        'success' => 'manual',
                        'warning' => 'auto',
                        'danger' => 'timeout',
                    ])
                    ->placeholder('Active Session'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->getStateUsing(fn ($record) => is_null($record->logout_at)),
            ])
            ->filters([
                SelectFilter::make('logout_type')
                    ->label('Logout Type')
                    ->options([
                        'manual' => 'Manual Logout',
                        'auto' => 'Automatic Logout',
                        'timeout' => 'Session Timeout',
                    ]),
                Filter::make('active_sessions')
                    ->label('Active Sessions Only')
                    ->query(fn (Builder $query): Builder => $query->whereNull('logout_at')),
                Filter::make('recent_activity')
                    ->label('Last 7 Days')
                    ->query(fn (Builder $query): Builder => $query->where('login_at', '>=', now()->subDays(7))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('login_at', 'desc');
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
            'index' => Pages\ListStudentActivities::route('/'),
            'overview' => Pages\StudentActivityOverview::route('/overview'),
            'create' => Pages\CreateStudentActivity::route('/create'),
            'edit' => Pages\EditStudentActivity::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNull('logout_at')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $activeCount = static::getModel()::whereNull('logout_at')->count();
        return $activeCount > 0 ? 'warning' : 'success';
    }
}