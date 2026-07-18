<nav x-data="{ open: false }" class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center">
                            <x-application-logo class="w-5 h-5 fill-current text-white" />
                        </span>
                        <span class="hidden sm:block font-semibold text-slate-800 dark:text-slate-100">{{ config('app.name') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (Auth::user()->esAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Panel
                        </x-nav-link>
                        <x-nav-link :href="route('admin.tareas.index')" :active="request()->routeIs('admin.tareas.*')">
                            Tareas
                        </x-nav-link>
                        <x-nav-link :href="route('admin.clientes.index')" :active="request()->routeIs('admin.clientes.*')">
                            Clientes
                        </x-nav-link>
                        <x-nav-link :href="route('admin.operarios.index')" :active="request()->routeIs('admin.operarios.*')">
                            Operarios
                        </x-nav-link>
                        <x-nav-link :href="route('admin.fichajes.index')" :active="request()->routeIs('admin.fichajes.*')">
                            Fichajes
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('operario.tareas.index')" :active="request()->routeIs('operario.tareas.index')">
                            Mis tareas de hoy
                        </x-nav-link>
                        <x-nav-link :href="route('operario.tareas.historial')" :active="request()->routeIs('operario.tareas.historial')">
                            Historial
                        </x-nav-link>
                        <x-nav-link :href="route('operario.fichajes.index')" :active="request()->routeIs('operario.fichajes.index')">
                            Fichar
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-4">
                @if (Auth::user()->esAdmin())
                    <a href="{{ route('admin.herramientas.index') }}" class="inline-flex items-center justify-center p-2 rounded-md text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-slate-200 transition duration-150 ease-in-out" title="Configuración" aria-label="Configuración">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>
                @endif
                <x-theme-toggle />

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-slate-400 bg-white dark:bg-slate-800 hover:text-gray-700 dark:hover:text-slate-200 focus:outline-none transition ease-in-out duration-150">
                            <x-avatar :src="Auth::user()->avatarUrl()" :iniciales="Auth::user()->iniciales()" class="w-7 h-7 text-xs mr-2" />
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Perfil
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center gap-3 sm:hidden">
                <x-theme-toggle />
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-slate-500 hover:text-gray-500 dark:hover:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-slate-700 focus:text-gray-500 dark:focus:text-slate-300 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (Auth::user()->esAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Panel
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.tareas.index')" :active="request()->routeIs('admin.tareas.*')">
                    Tareas
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.clientes.index')" :active="request()->routeIs('admin.clientes.*')">
                    Clientes
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.operarios.index')" :active="request()->routeIs('admin.operarios.*')">
                    Operarios
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.fichajes.index')" :active="request()->routeIs('admin.fichajes.*')">
                    Fichajes
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.herramientas.index')" :active="request()->routeIs('admin.herramientas.*')">
                    Configuración
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('operario.tareas.index')" :active="request()->routeIs('operario.tareas.index')">
                    Mis tareas de hoy
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('operario.tareas.historial')" :active="request()->routeIs('operario.tareas.historial')">
                    Historial
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('operario.fichajes.index')" :active="request()->routeIs('operario.fichajes.index')">
                    Fichar
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-slate-700">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-slate-100">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-slate-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Perfil
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Cerrar sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
