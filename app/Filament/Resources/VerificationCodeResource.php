<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificationCodeResource\Pages;
use App\Models\VerificationCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VerificationCodeResource extends Resource
{
    protected static ?string $model = VerificationCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    
    protected static ?string $navigationLabel = 'Verification Codes';
    
    protected static ?string $modelLabel = 'Verification Code';
    
    protected static ?string $pluralModelLabel = 'Verification Codes';
    
    protected static ?string $navigationGroup = 'Students';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Verification Code Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Code Name')
                            ->placeholder('e.g., Course Batch 1')
                            ->helperText('Optional name to identify this verification code'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Describe the purpose of this verification code')
                            ->rows(3),
                        
                        Forms\Components\TextInput::make('code')
                            ->label('Verification Code')
                            ->placeholder('Leave empty to auto-generate')
                            ->helperText('Leave empty to auto-generate a random code')
                            ->maxLength(20),
                    ])->columns(1),
                
                Forms\Components\Section::make('Course Assignment')
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->label('Course')
                            ->options(\App\Models\Course::pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('subject_id', null)),
                        
                        Forms\Components\Select::make('subject_id')
                            ->label('Subject')
                            ->options(function (callable $get) {
                                $courseId = $get('course_id');
                                if (!$courseId) {
                                    return [];
                                }
                                return \App\Models\Subject::where('course_id', $courseId)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active codes can be used'),
                        
                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Expires At')
                            ->helperText('Leave empty for no expiration')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No name'),
                
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Course')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_used')
                    ->label('Used')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('usedBy.name')
                    ->label('Used By')
                    ->placeholder('Not used')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('used_at')
                    ->label('Used At')
                    ->dateTime()
                    ->placeholder('Not used')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_used')
                    ->label('Usage Status')
                    ->placeholder('All codes')
                    ->trueLabel('Used codes')
                    ->falseLabel('Unused codes'),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All codes')
                    ->trueLabel('Active codes')
                    ->falseLabel('Inactive codes'),
                
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListVerificationCodes::route('/'),
            'create' => Pages\CreateVerificationCode::route('/create'),
            'view' => Pages\ViewVerificationCode::route('/{record}'),
            'edit' => Pages\EditVerificationCode::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hide verification codes from teachers
        return auth()->check() && !auth()->user()->isTeacher();
    }
}

