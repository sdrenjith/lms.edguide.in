<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoubtResource\Pages;
use App\Models\Doubt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DoubtResource extends Resource
{
    protected static ?string $model = Doubt::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Doubt Clearance';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('student_name')
                        ->label('Student Name')
                        ->disabled()
                        ->default(fn ($record) => $record?->user?->name ?? 'Unknown Student')
                        ->dehydrated(false)
                        ->extraAttributes([
                            'style' => 'color: #000000 !important; font-weight: 600 !important; background-color: #ffffff !important;'
                        ]),
                    Forms\Components\Textarea::make('message')
                        ->label('Student Doubt')
                        ->disabled()
                        ->rows(3)
                        ->extraAttributes([
                            'style' => 'color: #000000 !important; background-color: #f9fafb; font-weight: 400 !important;'
                        ]),
                    Forms\Components\Textarea::make('reply')
                        ->label('Admin Reply')
                        ->rows(4)
                        ->placeholder('Type your reply here...'),
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
                Tables\Columns\TextColumn::make('message')
                    ->label('Doubt')
                    ->wrap()
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->message;
                    }),
                Tables\Columns\TextColumn::make('reply')
                    ->label('Reply')
                    ->wrap()
                    ->limit(40)
                    ->placeholder('No reply yet')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(function ($record) {
                        return $record->reply ?? 'No reply yet';
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Asked At')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('replied_at')
                    ->label('Replied At')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->placeholder('Not replied')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                Tables\Columns\IconColumn::make('has_reply')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->getStateUsing(fn ($record) => !empty($record->reply)),
            ])
            ->filters([
                Tables\Filters\Filter::make('replied')
                    ->query(fn ($query) => $query->whereNotNull('reply'))
                    ->label('Replied'),
                Tables\Filters\Filter::make('pending')
                    ->query(fn ($query) => $query->whereNull('reply'))
                    ->label('Pending Reply'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Reply'),
                Tables\Actions\DeleteAction::make()
                    ->color('danger'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoubts::route('/'),
            'edit' => Pages\EditDoubt::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Show for admin and teacher users
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'teacher']);
    }
} 