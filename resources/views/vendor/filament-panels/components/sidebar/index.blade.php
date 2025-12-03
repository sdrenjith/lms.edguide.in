@vite(['resources/css/app.css'])

@props([
    'navigation',
])

@php
    if (filament()->getCurrentPanel()?->getId() === 'student') {
        return;
    }

    $openSidebarClasses = 'fi-sidebar-open w-[--sidebar-width] translate-x-0 shadow-xl ring-1 ring-gray-950/5 dark:ring-white/10 rtl:-translate-x-0';
    $isRtl = __('filament-panels::layout.direction') === 'rtl';
@endphp

{{-- format-ignore-start --}}
<aside
    x-data="{}"
    @if (filament()->isSidebarCollapsibleOnDesktop() && (! filament()->hasTopNavigation()))
        x-cloak
        x-bind:class="
            $store.sidebar.isOpen
                ? @js($openSidebarClasses . ' ' . 'lg:sticky')
                : '-translate-x-full rtl:translate-x-full lg:sticky lg:translate-x-0 rtl:lg:-translate-x-0'
        "
    @else
        @if (filament()->hasTopNavigation())
            x-cloak
            x-bind:class="$store.sidebar.isOpen ? @js($openSidebarClasses) : '-translate-x-full rtl:translate-x-full'"
        @elseif (filament()->isSidebarFullyCollapsibleOnDesktop())
            x-cloak
            x-bind:class="$store.sidebar.isOpen ? @js($openSidebarClasses . ' ' . 'lg:sticky') : '-translate-x-full rtl:translate-x-full'"
        @else
            x-cloak="-lg"
            x-bind:class="
                $store.sidebar.isOpen
                    ? @js($openSidebarClasses . ' ' . 'lg:sticky')
                    : 'w-[--sidebar-width] -translate-x-full rtl:translate-x-full lg:sticky'
            "
        @endif
    @endif
    {{
        $attributes->class([
            'fi-sidebar fixed inset-y-0 start-0 z-30 flex flex-col h-screen content-start bg-white transition-all dark:bg-gray-900 lg:z-0 lg:bg-transparent lg:shadow-none lg:ring-0 lg:transition-none dark:lg:bg-transparent',
            'lg:translate-x-0 rtl:lg:-translate-x-0' => ! (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop() || filament()->hasTopNavigation()),
            'lg:-translate-x-full rtl:lg:translate-x-full' => filament()->hasTopNavigation(),
        ])
    }}
>
    <div class="overflow-x-clip">
        <header
            class="fi-sidebar-header flex h-16 items-center bg-white px-6 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lg:shadow-sm"
        >
            <div
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-show="$store.sidebar.isOpen"
                    x-transition:enter="lg:transition lg:delay-100"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                @endif
            >
                @if ($homeUrl = filament()->getHomeUrl())
                    <a {{ \Filament\Support\generate_href_html($homeUrl) }}>
                        <img src="/edguide-logo.png" alt="EdGuide" class="max-w-full h-auto p-2" style="width:170px; height:60px;" />
                    </a>
                @else
                    <img src="/edguide-logo.png" alt="EdGuide" class="max-w-full h-auto p-2" style="width:250px; height:60px;" />
                @endif
            </div>

            @if (filament()->isSidebarCollapsibleOnDesktop())
                <x-filament::icon-button
                    color="gray"
                    :icon="$isRtl ? 'heroicon-o-chevron-left' : 'heroicon-o-chevron-right'"
                    {{-- @deprecated Use `panels::sidebar.expand-button.rtl` instead of `panels::sidebar.expand-button` for RTL. --}}
                    :icon-alias="$isRtl ? ['panels::sidebar.expand-button.rtl', 'panels::sidebar.expand-button'] : 'panels::sidebar.expand-button'"
                    icon-size="lg"
                    :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                    x-cloak
                    x-data="{}"
                    x-on:click="$store.sidebar.open()"
                    x-show="! $store.sidebar.isOpen"
                    class="mx-auto"
                />
            @endif

            @if (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop())
                <x-filament::icon-button
                    color="gray"
                    :icon="$isRtl ? 'heroicon-o-chevron-right' : 'heroicon-o-chevron-left'"
                    {{-- @deprecated Use `panels::sidebar.collapse-button.rtl` instead of `panels::sidebar.collapse-button` for RTL. --}}
                    :icon-alias="$isRtl ? ['panels::sidebar.collapse-button.rtl', 'panels::sidebar.collapse-button'] : 'panels::sidebar.collapse-button'"
                    icon-size="lg"
                    :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                    x-cloak
                    x-data="{}"
                    x-on:click="$store.sidebar.close()"
                    x-show="$store.sidebar.isOpen"
                    class="ms-auto hidden lg:flex"
                />
            @endif
        </header>
    </div>

    <div class="flex items-center justify-between mb-4">
        {{-- Remove locale/language label --}}
    </div>

    <nav
        class="fi-sidebar-nav flex-grow flex flex-col gap-y-7 overflow-y-auto overflow-x-hidden px-6 py-8"
        style="scrollbar-gutter: stable"
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_NAV_START) }}

        <ul class="fi-sidebar-nav-groups -mx-2 flex flex-col gap-y-7">
            @foreach ($navigation as $group)
                <x-filament-panels::sidebar.group
                    :active="$group->isActive()"
                    :collapsible="$group->isCollapsible()"
                    :icon="$group->getIcon()"
                    :items="$group->getItems()"
                    :label="$group->getLabel()"
                    :attributes="\Filament\Support\prepare_inherited_attributes($group->getExtraSidebarAttributeBag())"
                />
            @endforeach
        </ul>

        <!-- {{-- Dynamic Available Courses menu with Subjects --}}
        @if (!auth()->check() || (!auth()->user()->isDataManager() && !auth()->user()->isAccounts() && !auth()->user()->isDataEntry()))
        @php
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
        @endphp
        <div class="fi-sidebar-group mb-6">
            <div class="fi-sidebar-group-label px-2 py-2 text-xs font-bold tracking-wider text-gray-500 uppercase">Available Courses</div>
            <ul>
                @foreach($courses as $course)
                    <li class="pl-2 py-1" x-data="{ open: false }">
                        <div @click="open = !open" class="cursor-pointer flex items-center ac">
                            <x-heroicon-o-academic-cap class="w-4 h-4 inline" />
                            <span class="ml-1 text-primary-600 hover:underline">{{ $course->name }}</span>
                            <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 20 20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8l4 4 4-4" /></svg>
                        </div>
                        <ul x-show="open" x-transition class="pl-2">
                            @foreach($subjects as $subject)
                                @php
                                    $hasQuestions = false;
                                    $subjectDays = collect();
                                    foreach ($questions as $key => $questionGroup) {
                                        list($courseId, $subjectId, $dayId) = explode('-', $key);
                                        if ($courseId == $course->id && $subjectId == $subject->id && isset($days[$dayId])) {
                                            $hasQuestions = true;
                                            $subjectDays->put($dayId, $days[$dayId]);
                                        }
                                    }
                                @endphp
                                @if($hasQuestions)
                                    <li class="pl-4 py-1" x-data="{ open: false }">
                                        <div @click="open = !open" class="cursor-pointer flex items-center">
                                            <x-heroicon-o-rectangle-stack class="w-4 h-4 inline" />
                                            <span class="ml-1 text-green-600 hover:underline">{{ $subject->name }}</span>
                                            <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 20 20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8l4 4 4-4" /></svg>
                                        </div>
                                        <ul x-show="open" x-transition class="pl-2">
                                            @foreach($subjectDays as $day)
                                                @php
                                                    $dayQuestions = $questions->get($course->id . '-' . $subject->id . '-' . $day->id, collect());
                                                @endphp
                                                <li class="pl-6 py-1" x-data="{ open: false }">
                                                    <div @click="open = !open" class="cursor-pointer flex items-center">
                                                        <x-heroicon-o-calendar class="w-4 h-4 inline" />
                                                        <span class="ml-1 text-yellow-600 hover:underline">{{ $day->title }}</span>
                                                        <span class="ml-1 text-xs text-gray-500">({{ $dayQuestions->count() }})</span>
                                                        <svg :class="{'rotate-90': open}" class="w-4 h-4 ml-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 20 20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8l4 4 4-4" /></svg>
                                                    </div>
                                                    <ul x-show="open" x-transition>
                                                        @foreach($dayQuestions as $question)
                                                            <li class="pl-8 py-1">
                                                                <a href="#" class="text-blue-600 hover:underline text-sm">
                                                                    <x-heroicon-o-light-bulb class="w-4 h-4 inline" /> {{ \Illuminate\Support\Str::limit($question->question_text ?? $question->instruction ?? 'Question', 35) }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        {{-- End Dynamic Available Courses menu --}} -->

        @if (filament()->hasTenancy() && filament()->hasTenantMenu())
            <div
                @class([
                    'fi-sidebar-nav-tenant-menu-ctn',
                    '-mx-2' => ! filament()->isSidebarCollapsibleOnDesktop(),
                ])
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-bind:class="$store.sidebar.isOpen ? '-mx-2' : '-mx-4'"
                @endif
            >
                <x-filament-panels::tenant-menu />
            </div>
        @endif

        <script>
            var collapsedGroups = JSON.parse(
                localStorage.getItem('collapsedGroups'),
            )

            if (collapsedGroups === null || collapsedGroups === 'null') {
                localStorage.setItem(
                    'collapsedGroups',
                    JSON.stringify(@js(
                        collect($navigation)
                            ->filter(fn (\Filament\Navigation\NavigationGroup $group): bool => $group->isCollapsed())
                            ->map(fn (\Filament\Navigation\NavigationGroup $group): string => $group->getLabel())
                            ->values()
                            ->all()
                    )),
                )
            }

            collapsedGroups = JSON.parse(
                localStorage.getItem('collapsedGroups'),
            )

            document
                .querySelectorAll('.fi-sidebar-group')
                .forEach((group) => {
                    if (
                        !collapsedGroups.includes(group.dataset.groupLabel)
                    ) {
                        return
                    }

                    // Alpine.js loads too slow, so attempt to hide a
                    // collapsed sidebar group earlier.
                    group.querySelector(
                        '.fi-sidebar-group-items',
                    ).style.display = 'none'
                    group
                        .querySelector('.fi-sidebar-group-collapse-button')
                        .classList.add('rotate-180')
                })
        </script>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_NAV_END) }}
    </nav>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_FOOTER) }}
</aside>
{{-- format-ignore-end --}}
