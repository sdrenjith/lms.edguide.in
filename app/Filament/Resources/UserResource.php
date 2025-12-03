<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Enter the user\'s basic information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter full name'),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Enter email address'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->dehydrated(fn ($state): bool => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->placeholder('Enter password'),
                                Forms\Components\Select::make('role')
                                    ->label('User Role')
                                    ->required()
                                    ->options(self::getAvailableRoles())
                                    ->placeholder('Select user role')
                                    ->helperText('Choose the appropriate role for this user'),
                            ]),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Student Information')
                    ->description('Student registration details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('guardian_name')
                                    ->label('Guardian Name')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('dob')
                                    ->label('Date of Birth'),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20),
                                Forms\Components\Select::make('gender')
                                    ->label('Gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                        'other' => 'Other',
                                    ]),
                                Forms\Components\TextInput::make('nationality')
                                    ->label('Nationality')
                                    ->maxLength(100),
                                Forms\Components\Select::make('qualification')
                                    ->label('Qualification')
                                    ->options([
                                        'High School' => 'High School',
                                        'Diploma' => 'Diploma',
                                        'Bachelor\'s Degree' => 'Bachelor\'s Degree',
                                        'Master\'s Degree' => 'Master\'s Degree',
                                        'PhD' => 'PhD',
                                        'Other' => 'Other',
                                    ]),
                                Forms\Components\Select::make('batch_id')
                                    ->label('Batch')
                                    ->relationship('batch', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Textarea::make('address')
                                    ->label('Address Information')
                                    ->maxLength(1000)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('course_fee')
                                    ->label('Course Fee')
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => $record?->role === 'student'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'teacher' => 'warning',
                        'student' => 'info',
                        'accounts' => 'success',
                        'dataentry' => 'gray',
                        'datamanager' => 'primary',
                        'manager' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'teacher' => 'Teacher',
                        'student' => 'Student',
                        'accounts' => 'Accounts',
                        'dataentry' => 'Data Entry',
                        'datamanager' => 'Data Manager',
                        'manager' => 'Manager',
                        default => ucfirst($state),
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter by Role')
                    ->options(self::getAvailableRoles())
                    ->placeholder('All Roles'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hide for datamanager, teacher, accounts, dataentry, and manager users
        return !(auth()->check() && (auth()->user()->isDataManager() || auth()->user()->isTeacher() || auth()->user()->isAccounts() || auth()->user()->isDataEntry() || auth()->user()->isManager()));
    }

    public static function getAvailableRoles(): array
    {
        // Only show admin, teacher, and student roles for now
        // Other roles are temporarily hidden/disabled
        $availableRoles = [
            'admin' => 'Admin',
            'teacher' => 'Teacher', 
            'student' => 'Student',
        ];
        
        // Commented out roles - temporarily hidden
        // 'accounts' => 'Accounts',
        // 'dataentry' => 'Data Entry',
        // 'datamanager' => 'Data Manager',
        // 'manager' => 'Manager',
        
        // Get unique roles from the database that are in our available list
        $roles = User::distinct()->pluck('role')->filter()->toArray();
        
        // Only include roles that are in our available roles list
        $filteredRoles = [];
        foreach ($roles as $role) {
            if (isset($availableRoles[$role])) {
                $filteredRoles[$role] = $availableRoles[$role];
            }
        }
        
        // Add any available roles that don't exist in the database yet
        foreach ($availableRoles as $key => $value) {
            if (!isset($filteredRoles[$key])) {
                $filteredRoles[$key] = $value;
            }
        }
        
        // Sort alphabetically by display name
        asort($filteredRoles);
        
        return $filteredRoles;
    }
}
