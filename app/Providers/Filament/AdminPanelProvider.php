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
use App\Models\Course;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Illuminate\Support\Str;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Vite;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\StudentMiddleware;
use Filament\Facades\Filament;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->defaultAvatarProvider(\Filament\AvatarProviders\UiAvatarsProvider::class)
            ->homeUrl(function () {
                if (auth()->check() && auth()->user()->isAccounts()) {
                    return route('filament.admin.resources.students.index');
                }
                if (auth()->check() && auth()->user()->isDataEntry()) {
                    return route('filament.admin.resources.notes.index');
                }
                return route('filament.admin.pages.dashboard');
            })
            ->colors([
                'primary' => Color::Teal,
            ])
            ->darkMode(true)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->assets([
                Css::make('custom-checkbox-fix', 'resources/css/filament/admin/custom.css'),
                Css::make('filament-upload-styles', 'resources/css/filament-upload-styles.css'),
            ])
            ->brandName('EdGuide')
            ->brandLogo(asset('edguide-logo.png'))
            ->favicon(asset('images/favicon.ico'))
            ->spa()
            ->pages($this->getPagesForUser())
            ->resources($this->getResourcesForUser())
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                AdminMiddleware::class,
            ])
            ->navigationItems(array_merge([
                NavigationItem::make('Dashboard')
                    ->icon('heroicon-o-home')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                    ->url(fn (): string => route('filament.admin.pages.dashboard'))
                    ->visible(fn (): bool => auth()->check() && !auth()->user()->isAccounts() && !auth()->user()->isDataEntry()),

                NavigationItem::make('Register Student')
                    ->icon('heroicon-o-user-plus')
                    ->group('Students')
                    ->url(fn (): string => route('filament.admin.resources.students.create'))
                    ->visible(fn (): bool => auth()->user()->isAdmin() || auth()->user()->isAccounts()),
                NavigationItem::make('All Students')
                    ->icon('heroicon-o-users')
                    ->group('Students')
                    ->url(fn (): string => route('filament.admin.resources.students.index'))
                    ->visible(fn (): bool => auth()->user()->isAdmin() || auth()->user()->isAccounts() || auth()->user()->isManager()),
                NavigationItem::make('Student Activity')
                    ->icon('heroicon-o-clock')
                    ->group('Students')
                    ->url(fn (): string => route('filament.admin.resources.student-activities.overview'))
                    ->visible(fn (): bool => auth()->user()->isAdmin() || auth()->user()->isManager()),

                // NavigationItem::make('Fee Payments')
                //     ->icon('heroicon-o-credit-card')
                //     ->group('Financial Management')
                //     ->url(fn (): string => route('filament.admin.resources.fees.index'))
                //     ->visible(fn (): bool => auth()->user()->isAdmin() || auth()->user()->isAccounts() || auth()->user()->isManager()),
                // NavigationItem::make('Fee Summaries')
                //     ->icon('heroicon-o-chart-bar')
                //     ->group('Financial Management')
                //     ->url(fn (): string => route('filament.admin.resources.fee-summaries.index'))
                //     ->visible(fn (): bool => auth()->user()->isAdmin() || auth()->user()->isAccounts() || auth()->user()->isManager()),
                // NavigationItem::make('Live Class')
                //     ->icon('heroicon-o-microphone')
                //     ->url(fn (): string => route('filament.admin.resources.speaking-sessions.index'))
                //     ->visible(fn (): bool => auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher()) && !auth()->user()->isAccounts() && !auth()->user()->isDataEntry()),
                // NavigationItem::make('Doubt Clearance')
                //     ->icon('heroicon-o-chat-bubble-left-right')
                //     ->url(fn (): string => route('filament.admin.resources.doubts.index'))
                //     ->visible(fn (): bool => auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher()) && !auth()->user()->isAccounts() && !auth()->user()->isDataEntry()),
                NavigationItem::make('Notes')
                    ->icon('heroicon-o-document-text')
                    ->group('Content Management')
                    ->url(fn (): string => route('filament.admin.resources.notes.index'))
                    ->visible(fn (): bool => auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isAccounts())),
                NavigationItem::make('Videos')
                    ->icon('heroicon-o-video-camera')
                    ->group('Content Management')
                    ->url(fn (): string => route('filament.admin.resources.videos.index'))
                    ->visible(fn (): bool => auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isAccounts())),
            ], [])) // $this->getCourseNavigationItems() - temporarily disabled
            ->navigationGroups($this->getNavigationGroupsForUser());
    }

    protected function getNavigationGroupsForUser(): array
    {
        if (auth()->check() && auth()->user()->isAccounts()) {
            // For accounts users, show these groups including content management
            return [
                NavigationGroup::make()
                    ->label('Students'),
                // NavigationGroup::make()
                //     ->label('Financial Management'),
                NavigationGroup::make()
                    ->label('Content Management'),
            ];
        }
        
        if (auth()->check() && auth()->user()->isDataEntry()) {
            // For dataentry users, show no navigation groups (content management removed)
            return [];
        }
        
        if (auth()->check() && auth()->user()->isManager()) {
            // For manager users, show specific groups (no Content Management)
            return [
                NavigationGroup::make()
                    ->label('Students'),
                NavigationGroup::make()
                    ->label('Batch Management'),
                // NavigationGroup::make()
                //     ->label('Financial Management'),
                // NavigationGroup::make()
                //     ->label('Test Management'),
            ];
        }
        
        // For all other users, show all groups including content management
        return [
            NavigationGroup::make()
                ->label('Students'),
            NavigationGroup::make()
                ->label('Batch Management'),
            // NavigationGroup::make()
            //     ->label('Financial Management'),
            NavigationGroup::make()
                ->label('Content Management'),
            // NavigationGroup::make()
            //     ->label('Test Management'),
            // NavigationGroup::make()
            //     ->label('A1 Course')
            //     ->collapsible(),
            // NavigationGroup::make()
            //     ->label('A2 Course')
            //     ->collapsible(),
            // NavigationGroup::make()
            //     ->label('B1 Course')
            //     ->collapsible(),
            // NavigationGroup::make()
            //     ->label('B2 Course')
            //     ->collapsible(),
        ];
    }

    protected function getPagesForUser(): array
    {
        if (auth()->check() && (auth()->user()->isAccounts() || auth()->user()->isDataEntry())) {
            // For accounts and dataentry users, don't show any pages (no dashboard)
            return [];
        }
        
        // For all other users, show dashboard
        return [
            \App\Filament\Pages\Dashboard::class,
        ];
    }

    protected function getResourcesForUser(): array
    {
        if (auth()->check() && auth()->user()->isAccounts()) {
            // For accounts users, show these resources including notes and videos
            return [
                \App\Filament\Resources\BatchResource::class,
                \App\Filament\Resources\StudentResource::class,
                \App\Filament\Resources\FeeResource::class,
                \App\Filament\Resources\FeeSummaryResource::class,
                \App\Filament\Resources\NoteResource::class,
                \App\Filament\Resources\VideoResource::class,
                \App\Filament\Resources\VerificationCodeResource::class,
            ];
        }
        
        if (auth()->check() && auth()->user()->isDataEntry()) {
            // For dataentry users, no resources (content management removed)
            return [];
        }
        
        if (auth()->check() && auth()->user()->isManager()) {
            // For manager users, show read-only resources (no content management)
            return [
                \App\Filament\Resources\CourseResource::class,
                \App\Filament\Resources\BatchResource::class,
                \App\Filament\Resources\StudentResource::class,
                \App\Filament\Resources\StudentActivityResource::class,
                \App\Filament\Resources\FeeResource::class,
                \App\Filament\Resources\FeeSummaryResource::class,
                \App\Filament\Resources\SubjectResource::class,
                \App\Filament\Resources\TestResource::class,
                \App\Filament\Resources\OpinionVerificationResource::class,
                \App\Filament\Resources\VerificationCodeResource::class,
            ];
        }
        
        if (auth()->check() && auth()->user()->isTeacher()) {
            // For teachers, show limited resources (no verification codes, user management, etc.)
            return [
                \App\Filament\Resources\BatchResource::class,
                \App\Filament\Resources\StudentResource::class,
                \App\Filament\Resources\StudentActivityResource::class,
                \App\Filament\Resources\SubjectResource::class,
                \App\Filament\Resources\QuestionResource::class,
                \App\Filament\Resources\TestResource::class,
                \App\Filament\Resources\OpinionVerificationResource::class,
            ];
        }
        
        // For all other users, show all resources including content management
        return [
            \App\Filament\Resources\CourseResource::class,
            \App\Filament\Resources\BatchResource::class,
            \App\Filament\Resources\StudentResource::class,
            \App\Filament\Resources\StudentActivityResource::class,
            \App\Filament\Resources\FeeResource::class,
            \App\Filament\Resources\FeeSummaryResource::class,
            \App\Filament\Resources\SpeakingSessionResource::class,
            \App\Filament\Resources\DoubtResource::class,
            \App\Filament\Resources\UserResource::class,
            \App\Filament\Resources\SubjectResource::class,
            \App\Filament\Resources\QuestionResource::class,
            \App\Filament\Resources\QuestionMediaResource::class,
            \App\Filament\Resources\OptionResource::class,
            \App\Filament\Resources\DayResource::class,
            \App\Filament\Resources\TestResource::class,
            \App\Filament\Resources\OpinionVerificationResource::class,
            \App\Filament\Resources\VerificationCodeResource::class,
            \App\Filament\Resources\NoteResource::class,
            \App\Filament\Resources\VideoResource::class,
        ];
    }

    protected function getCourseNavigationItems(): array
    {
        // COMPLETELY DISABLED - Hide all course navigation items (A1, A2, B1, B2 with subjects and questions)
        return [];
        
        // ALL ORIGINAL LOGIC DISABLED:
        // if (auth()->check() && (auth()->user()->isDataManager() || auth()->user()->isAccounts() || auth()->user()->isDataEntry())) {
        //     return [];
        // }
        
        try {
            $courses = \App\Models\Course::all();
            // Filter subjects based on user role
            if (auth()->check() && auth()->user()->isTeacher()) {
                $subjects = auth()->user()->subjects;
            } else {
                $subjects = \App\Models\Subject::all();
            }
            $questions = \App\Models\Question::all()->groupBy(function($q) {
                return $q->course_id . '-' . $q->subject_id . '-' . $q->day_id;
            });
            $days = \App\Models\Day::all()->keyBy('id');
            
            $items = [];
            
            foreach ($courses as $course) {
                $courseGroupName = $course->name . ' Course';
                
                // Add all subjects under each course
                foreach ($subjects as $subject) {
                    // Check if this subject has any questions for this course
                    $hasQuestions = false;
                    $subjectDays = collect();
                    
                    foreach ($questions as $key => $questionGroup) {
                        list($courseId, $subjectId, $dayId) = explode('-', $key);
                        if ($courseId == $course->id && $subjectId == $subject->id && isset($days[$dayId])) {
                            $hasQuestions = true;
                            $subjectDays->put($dayId, $days[$dayId]);
                        }
                    }
                    
                    if ($hasQuestions) {
                        $items[] = \Filament\Navigation\NavigationItem::make($subject->name)
                            ->group($courseGroupName)
                            ->icon('heroicon-o-rectangle-stack')
                            ->url('#')
                            ->isActiveWhen(fn () => false)
                            ->badge(function() use ($course, $subject, $questions) {
                                $count = 0;
                                foreach ($questions as $key => $questionGroup) {
                                    list($courseId, $subjectId, $dayId) = explode('-', $key);
                                    if ($courseId == $course->id && $subjectId == $subject->id) {
                                        $count += $questionGroup->count();
                                    }
                                }
                                return $count;
                            })
                            ->badgeColor('success')
                            ->sort($subject->id);
                        
                        // Add days under subject
                        foreach ($subjectDays as $day) {
                            $dayQuestions = $questions->get($course->id . '-' . $subject->id . '-' . $day->id, collect());
                            
                            $items[] = \Filament\Navigation\NavigationItem::make('→ ' . $day->title)
                                ->group($courseGroupName)
                                ->icon('heroicon-o-calendar')
                                ->url('#')
                                ->isActiveWhen(fn () => false)
                                ->badge($dayQuestions->count())
                                ->badgeColor('warning')
                                ->sort(100 + $subject->id * 10 + $day->id);
                        }
                    }
                }
            }
            
            return $items;
        } catch (\Exception $e) {
            // Return empty array if there's any error
            return [];
        }
    }
}