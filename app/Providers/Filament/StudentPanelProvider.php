<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\MenuItem;
use App\Models\Course;
use Illuminate\Support\Str;
use App\Http\Middleware\StudentMiddleware;
use App\Filament\Student\Pages\Profile;
use Filament\Facades\Filament;

class StudentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('student')
            ->path('student')
            ->login(\Filament\Pages\Auth\Login::class)
            ->homeUrl(fn (): string => route('filament.student.pages.profile'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->darkMode(false)
            ->brandName('EdGuide')
            ->brandLogo(asset('images/student-logo.png'))
            ->favicon(asset('images/favicon.ico'))
            ->discoverResources(in: app_path('Filament/Student/Resources'), for: 'App\\Filament\\Student\\Resources')
            ->discoverPages(in: app_path('Filament/Student/Pages'), for: 'App\\Filament\\Student\\Pages')
            ->pages([
                // \App\Filament\Student\Pages\Dashboard::class, // Hidden from navigation
                Profile::class,
                \App\Filament\Student\Pages\StudyMaterials::class,
                // \App\Filament\Student\Pages\OpinionVerification::class, // Temporarily disabled
                \App\Filament\Student\Pages\SpeakingSessions::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Student/Widgets'), for: 'App\\Filament\\Student\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                StudentMiddleware::class,
                \App\Http\Middleware\StudentVerificationMiddleware::class,
            ])
            ->navigationItems([
                // Custom navigation is handled in the dashboard template
            ]);
    }

    protected function registerPages(): void
    {
        Filament::registerPages([
            // \App\Filament\Student\Pages\Dashboard::class, // Hidden from navigation
        ]);
    }
} 