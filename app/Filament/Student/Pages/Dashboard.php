<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.student.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    public static function getRouteName(?string $panel = null): string
    {
        return 'filament.student.pages.dashboard';
    }

    // No widgets needed for custom dashboard
    public function getWidgets(): array
    {
        return [];
    }
} 