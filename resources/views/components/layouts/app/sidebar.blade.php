<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            function themeHandler() {
                return {
                    theme: localStorage.getItem('theme') || 'light',

                    init() {
                        this.theme = localStorage.getItem('theme') || 'light';
                        this.apply();

                        document.addEventListener('livewire:navigated', () => {
                            this.theme = localStorage.getItem('theme') || 'light';
                            this.apply();
                        });
                    },

                    apply() {
                        document.documentElement.classList.toggle('dark', this.theme === 'dark');
                    },

                    toggle() {
                        this.theme = this.theme === 'dark' ? 'light' : 'dark';
                        localStorage.setItem('theme', this.theme);
                        this.apply();
                    }
                }
            }
        </script>

        @include('partials.head')
    </head>

    <body
        x-data="themeHandler()"
        x-init="init()"
        class="min-h-screen bg-zinc-100 text-zinc-800 transition-colors duration-300 dark:bg-zinc-950 dark:text-zinc-100"
    >
        <flux:sidebar
            sticky
            stashable
            class="border-e border-zinc-200 bg-zinc-50 px-3 py-3 dark:border-zinc-800 dark:bg-zinc-900"
        >
            <flux:sidebar.toggle
                class="lg:hidden rounded-lg p-2 text-zinc-600 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-800"
                icon="x-mark"
            />

            <a
                href="{{ route('home') }}"
                class="me-5 flex items-center space-x-2 rounded-xl px-2 py-2 transition hover:bg-zinc-100 dark:hover:bg-zinc-800 rtl:space-x-reverse"
                wire:navigate
            >
                <x-app-logo />
            </a>

            <div class="mt-4 mx-2 rounded-xl border border-yellow-300 bg-yellow-100 p-3 text-sm font-medium text-yellow-800 dark:border-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-200">
                <div class="flex items-center space-x-2">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Sistema de Desarrollo</span>
                </div>
            </div>

            <!-- ✅ MENÚ PRINCIPAL -->
            <flux:navlist variant="outline" class="mt-4">
                <flux:navlist.group :heading="__('Menú Principal')" class="grid gap-1">
                    @can('Ver Inicio')
                    <flux:navlist.item
                        :href="route('home')"
                        :current="request()->routeIs('home')"
                        wire:navigate
                        class="rounded-xl px-3 py-2.5 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    >
                        <i class="bi bi-house-door-fill me-2"></i> {{ __('Inicio') }}
                    </flux:navlist.item>
                    @endcan

                </flux:navlist.group>

                <!-- ✅ GESTIÓN DE PERSONAL -->
                @canany(['Ver Personas', 'Ver Empleados', 'Ver Cargos', 'Ver Departamentos', 'Ver Contratos', 'Ver Documentos'])
                <flux:navlist.group :heading="__('Gestión de Personal')" class="grid mt-3">
                    @php
                        $isStaffSection = request()->routeIs('personas*') || request()->routeIs('empleados*')
                            || request()->routeIs('cargos*') || request()->routeIs('departamentos*')
                            || request()->routeIs('contratos*') || request()->routeIs('documentos*');
                    @endphp

                    <details class="group" @if($isStaffSection) open @endif>
                        <summary class="flex items-center cursor-pointer rounded-xl px-3 py-2.5 transition hover:bg-zinc-200 dark:hover:bg-zinc-800 {{ $isStaffSection ? 'bg-zinc-200 font-semibold text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-700 dark:text-zinc-200' }}">
                            <i class="bi bi-person-vcard-fill me-2"></i>
                            <span>{{ __('Gestión de Personal') }}</span>
                            <i class="bi bi-chevron-down ms-auto transition-transform duration-200 group-open:rotate-180"></i>
                        </summary>

                        <div class="ml-5 mt-2 space-y-1 border-l border-zinc-200 pl-3 dark:border-zinc-700">
                            @can('Ver Personas')
                            <flux:navlist.item
                                :href="route('personas')"
                                :current="request()->routeIs('personas*')"
                                wire:navigate
                                class="{{ request()->routeIs('personas*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-person-badge-fill me-2"></i> {{ __('Personas') }}
                            </flux:navlist.item>
                            @endcan

                            @can('Ver Empleados')
                            <flux:navlist.item
                                :href="route('empleados')"
                                :current="request()->routeIs('empleados*')"
                                wire:navigate
                                class="{{ request()->routeIs('empleados*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-person-workspace me-2"></i> {{ __('Empleados') }}
                            </flux:navlist.item>
                            @endcan

                            @can('Ver Cargos')
                            <flux:navlist.item
                                :href="route('cargos')"
                                :current="request()->routeIs('cargos*')"
                                wire:navigate
                                class="{{ request()->routeIs('cargos*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-briefcase-fill me-2"></i> {{ __('Cargos') }}
                            </flux:navlist.item>
                            @endcan

                            @can('Ver Departamentos')
                            <flux:navlist.item
                                :href="route('departamentos')"
                                :current="request()->routeIs('departamentos*')"
                                wire:navigate
                                class="{{ request()->routeIs('departamentos*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-diagram-3-fill me-2"></i> {{ __('Departamentos') }}
                            </flux:navlist.item>
                            @endcan

                            @can('Ver Contratos')
                            <flux:navlist.item
                                :href="route('contratos')"
                                :current="request()->routeIs('contratos*')"
                                wire:navigate
                                class="{{ request()->routeIs('contratos*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-file-earmark-text-fill me-2"></i> {{ __('Contratos') }}
                            </flux:navlist.item>
                            @endcan

                            @can('Ver Documentos')
                            <flux:navlist.item
                                :href="route('documentos')"
                                :current="request()->routeIs('documentos*')"
                                wire:navigate
                                class="{{ request()->routeIs('documentos*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-folder-fill me-2"></i> {{ __('Documentos') }}
                            </flux:navlist.item>
                            @endcan
                        </div>
                    </details>
                </flux:navlist.group>
                @endcanany

                <!-- ✅ ASISTENCIA -->
                @canany(['Ver Asistencia', 'Ver Vacaciones', 'Ver Horas Extra'])
                <flux:navlist.group :heading="__('Asistencia')" class="grid mt-3">
                    @php
                        $isAttendanceSection = request()->routeIs('asistencia*') || request()->routeIs('turnos*')
                            || request()->routeIs('feriados*') || request()->routeIs('permisos-laborales*')
                            || request()->routeIs('vacaciones*') || request()->routeIs('horas-extra*');
                    @endphp

                    <details class="group" @if($isAttendanceSection) open @endif>
                        <summary class="flex items-center cursor-pointer rounded-xl px-3 py-2.5 transition hover:bg-zinc-200 dark:hover:bg-zinc-800 {{ $isAttendanceSection ? 'bg-zinc-200 font-semibold text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-700 dark:text-zinc-200' }}">
                            <i class="bi bi-clock-fill me-2"></i>
                            <span>{{ __('Asistencia') }}</span>
                            <i class="bi bi-chevron-down ms-auto transition-transform duration-200 group-open:rotate-180"></i>
                        </summary>

                        <div class="ml-5 mt-2 space-y-1 border-l border-zinc-200 pl-3 dark:border-zinc-700">
                            <flux:navlist.item
                                :href="route('asistencia')"
                                :current="request()->routeIs('asistencia*')"
                                wire:navigate
                                class="{{ request()->routeIs('asistencia*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-calendar-check-fill me-2"></i> {{ __('Marcajes') }}
                            </flux:navlist.item>

                            <flux:navlist.item
                                :href="route('permisos-laborales')"
                                :current="request()->routeIs('permisos-laborales*')"
                                wire:navigate
                                class="{{ request()->routeIs('permisos-laborales*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-envelope-paper-fill me-2"></i> {{ __('Permisos') }}
                            </flux:navlist.item>

                            <flux:navlist.item
                                :href="route('turnos')"
                                :current="request()->routeIs('turnos*')"
                                wire:navigate
                                class="{{ request()->routeIs('turnos*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-hourglass-split me-2"></i> {{ __('Turnos') }}
                            </flux:navlist.item>

                            <flux:navlist.item
                                :href="route('feriados')"
                                :current="request()->routeIs('feriados*')"
                                wire:navigate
                                class="{{ request()->routeIs('feriados*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-flag-fill me-2"></i> {{ __('Feriados') }}
                            </flux:navlist.item>

                            @can('Ver Vacaciones')
                            <flux:navlist.item
                                :href="route('vacaciones')"
                                :current="request()->routeIs('vacaciones*')"
                                wire:navigate
                                class="{{ request()->routeIs('vacaciones*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-airplane-fill me-2"></i> {{ __('Vacaciones') }}
                            </flux:navlist.item>
                            @endcan

                            @can('Ver Horas Extra')
                            <flux:navlist.item
                                :href="route('horas-extra')"
                                :current="request()->routeIs('horas-extra*')"
                                wire:navigate
                                class="{{ request()->routeIs('horas-extra*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-clock-history me-2"></i> {{ __('Horas Extra') }}
                            </flux:navlist.item>
                            @endcan
                        </div>
                    </details>
                </flux:navlist.group>
                @endcanany

                <!-- ✅ PLANILLAS -->
                @can('Ver Planillas')
                <flux:navlist.group :heading="__('Planillas')" class="grid mt-3">
                    @php
                        $isPayrollSection = request()->routeIs('planillas*') || request()->routeIs('conceptos-pago*');
                    @endphp

                    <details class="group" @if($isPayrollSection) open @endif>
                        <summary class="flex items-center cursor-pointer rounded-xl px-3 py-2.5 transition hover:bg-zinc-200 dark:hover:bg-zinc-800 {{ $isPayrollSection ? 'bg-zinc-200 font-semibold text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-700 dark:text-zinc-200' }}">
                            <i class="bi bi-cash-stack me-2"></i>
                            <span>{{ __('Planillas') }}</span>
                            <i class="bi bi-chevron-down ms-auto transition-transform duration-200 group-open:rotate-180"></i>
                        </summary>

                        <div class="ml-5 mt-2 space-y-1 border-l border-zinc-200 pl-3 dark:border-zinc-700">
                            <flux:navlist.item
                                :href="route('planillas')"
                                :current="request()->routeIs('planillas*')"
                                wire:navigate
                                class="{{ request()->routeIs('planillas*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-receipt me-2"></i> {{ __('Planillas') }}
                            </flux:navlist.item>

                            <flux:navlist.item
                                :href="route('conceptos-pago')"
                                :current="request()->routeIs('conceptos-pago*')"
                                wire:navigate
                                class="{{ request()->routeIs('conceptos-pago*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-list-check me-2"></i> {{ __('Conceptos de Pago') }}
                            </flux:navlist.item>
                        </div>
                    </details>
                </flux:navlist.group>
                @endcan

                <!-- ✅ CATÁLOGOS DEL SISTEMA -->
                @canany(['Ver Tipos', 'Ver Gestiones'])
                <flux:navlist.group :heading="__('Catálogos')" class="grid mt-3">
                    @php
                        $isCatalogSection = request()->routeIs('tipos*') || request()->routeIs('gestiones*');
                    @endphp

                    <details class="group" @if($isCatalogSection) open @endif>
                        <summary class="flex items-center cursor-pointer rounded-xl px-3 py-2.5 transition hover:bg-zinc-200 dark:hover:bg-zinc-800 {{ $isCatalogSection ? 'bg-zinc-200 font-semibold text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-700 dark:text-zinc-200' }}">
                            <i class="bi bi-collection-fill me-2"></i>
                            <span>{{ __('Catálogos') }}</span>
                            <i class="bi bi-chevron-down ms-auto transition-transform duration-200 group-open:rotate-180"></i>
                        </summary>

                        <div class="ml-5 mt-2 space-y-1 border-l border-zinc-200 pl-3 dark:border-zinc-700">
                            @can('Ver Tipos')
                            <flux:navlist.item
                                :href="route('tipos')"
                                :current="request()->routeIs('tipos*')"
                                wire:navigate
                                class="{{ request()->routeIs('tipos*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-tags-fill me-2"></i> {{ __('Tipos') }}
                            </flux:navlist.item>
                            @endcan

                            @can('Ver Gestiones')
                            <flux:navlist.item
                                :href="route('gestiones')"
                                :current="request()->routeIs('gestiones*')"
                                wire:navigate
                                class="{{ request()->routeIs('gestiones*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-calendar-range-fill me-2"></i> {{ __('Gestiones y Meses') }}
                            </flux:navlist.item>
                            @endcan
                        </div>
                    </details>
                </flux:navlist.group>
                @endcanany


                <!-- ✅ GESTIÓN DE USUARIOS -->
                @canany(['Ver Usuarios', 'Ver Roles', 'Ver Permisos'])
                <flux:navlist.group :heading="__('Gestión de Usuarios')" class="grid mt-3">
                    @php
                        $isUserSection = request()->routeIs('users*') || request()->routeIs('roles*') || request()->routeIs('permissions*');
                    @endphp

                    <details class="group" @if($isUserSection) open @endif>
                        <summary class="flex items-center cursor-pointer rounded-xl px-3 py-2.5 transition hover:bg-zinc-200 dark:hover:bg-zinc-800 {{ $isUserSection ? 'bg-zinc-200 font-semibold text-zinc-900 dark:bg-zinc-800 dark:text-white' : 'text-zinc-700 dark:text-zinc-200' }}">
                            <i class="bi bi-people-fill me-2"></i>
                            <span>{{ __('Usuarios') }}</span>
                            <i class="bi bi-chevron-down ms-auto transition-transform duration-200 group-open:rotate-180"></i>
                        </summary>

                        <div class="ml-5 mt-2 space-y-1 border-l border-zinc-200 pl-3 dark:border-zinc-700">
                            @can('Ver Usuarios')
                            <flux:navlist.item
                                :href="route('users')"
                                :current="request()->routeIs('users*')"
                                wire:navigate
                                class="{{ request()->routeIs('users*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-person-lines-fill me-2"></i> {{ __('Lista de Usuarios') }}
                            </flux:navlist.item>
                            @endcan

                            @can('Ver Roles')
                            <flux:navlist.item
                                :href="route('roles')"
                                :current="request()->routeIs('roles*')"
                                wire:navigate
                                class="{{ request()->routeIs('roles*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-person-gear me-2"></i> {{ __('Roles') }}
                            </flux:navlist.item>
                            @endcan

                            @can('Ver Permisos')
                            <flux:navlist.item
                                :href="route('permissions')"
                                :current="request()->routeIs('permissions*')"
                                wire:navigate
                                class="{{ request()->routeIs('permissions*') ? 'bg-zinc-200 dark:bg-zinc-800 font-semibold rounded-lg' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg' }}"
                            >
                                <i class="bi bi-shield-lock-fill me-2"></i> {{ __('Permisos') }}
                            </flux:navlist.item>
                            @endcan
                        </div>
                    </details>
                </flux:navlist.group>
                @endcanany
            </flux:navlist>

            <flux:spacer />

            <!-- ✅ MENÚ DE USUARIO (Desktop) -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px] dark:bg-zinc-900 dark:text-zinc-100">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-zinc-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                            {{ __('Opciones') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Cerrar Sesión') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>

            <!-- BOTÓN TEMA DESKTOP -->
            <div class="mt-3 hidden lg:flex justify-center px-2">
                <button
                    type="button"
                    @click="toggle()"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm font-medium text-zinc-800 shadow-sm transition hover:bg-zinc-100 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:hover:bg-zinc-700"
                >
                    <i class="bi bi-moon-stars-fill dark:hidden text-zinc-700"></i>
                    <i class="bi bi-sun-fill hidden dark:inline text-yellow-400"></i>
                    <span class="hidden dark:inline text-white" >Modo oscuro</span>
                    <span class="dark:hidden">Modo claro</span>
                </button>
            </div>
        </flux:sidebar>

        <!-- ✅ MENÚ MÓVIL -->
        <flux:header class="border-b border-zinc-200 bg-white px-3 py-2 dark:border-zinc-800 dark:bg-zinc-900 lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="toggle()"
                    class="inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700"
                >
                    <i class="bi bi-moon-stars-fill dark:hidden"></i>
                    <i class="bi bi-sun-fill hidden dark:inline"></i>
                </button>

                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />

                    <flux:menu class="dark:bg-zinc-900 dark:text-zinc-100">
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-zinc-700 dark:text-white">
                                            {{ auth()->user()->initials() }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                                {{ __('Opciones') }}
                            </flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Cerrar Sesión') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>