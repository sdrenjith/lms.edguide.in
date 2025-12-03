<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpeakingSessionResource\Pages;
use App\Models\SpeakingSession;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components;

class SpeakingSessionResource extends Resource
{
    protected static ?string $model = SpeakingSession::class;
    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $label = 'Live Class';
    protected static ?string $pluralLabel = 'Live Classes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Card::make()
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\Select::make('batch_id')
                                    ->label('Batch')
                                    ->options(\App\Models\Batch::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Components\TextInput::make('gmeet_link')
                                    ->label('GMeet Link')
                                    ->required(),
                                Components\Textarea::make('description')
                                    ->label('Description'),
                                Components\DatePicker::make('session_date')
                                    ->label('Session Date')
                                    ->required()
                                    ->default(now('Asia/Kolkata')->toDateString())
                                    ->minDate(now('Asia/Kolkata')->toDateString()),
                                Components\TimePicker::make('session_time')
                                    ->label('Session Time')
                                    ->required()
                                    ->seconds(false)
                                    ->default(now('Asia/Kolkata')->format('H:i')),
                            ]),
                        Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->extraAttributes(['class' => 'custom-green-toggle']),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('batch.name')
                    ->label('Batch')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('gmeet_link')
                    ->label('GMeet Link')
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->sortable(),
                Tables\Columns\TextColumn::make('session_date')
                    ->label('Date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('session_time')
                    ->label('Time')
                    ->sortable()
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::createFromFormat('H:i:s', $state)->format('h:i A')),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->defaultSort('session_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('batch_id')
                    ->label('Batch')
                    ->options(\App\Models\Batch::all()->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->color('danger'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpeakingSessions::route('/'),
            'create' => Pages\CreateSpeakingSession::route('/create'),
            'edit' => Pages\EditSpeakingSession::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return null;
    }

    public static function shouldRegisterNavigation(): bool {
        // Show for admin and teacher users
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher());
    }

    public static function canCreate(): bool {
        return true;
    }
} 