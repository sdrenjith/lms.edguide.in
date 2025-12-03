<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.student.pages.profile';
    protected static ?string $title = 'My Profile';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $slug = 'profile';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
    
    public static function getRouteName(?string $panel = null): string
    {
        return 'filament.student.pages.profile';
    }

    public function mount(): void
    {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('Filament Profile page - User verification status:', [
            'user_id' => $user->id,
            'is_verified' => $user->is_verified,
            'verification_code' => $user->verification_code,
            'verification_code_id' => $user->verification_code_id
        ]);
    }

    public function getViewData(): array
    {
        return [
            'user' => Auth::user(),
        ];
    }
} 