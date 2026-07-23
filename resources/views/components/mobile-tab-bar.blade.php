@php
    $esAdmin = Auth::user()->esAdmin();
@endphp

<nav
    x-data="{ masAbierto: false }"
    class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700"
    style="padding-bottom: env(safe-area-inset-bottom);"
>
    <!-- Panel "Más": enlaces secundarios, anclado justo encima de la barra -->
    <div
        x-show="masAbierto"
        x-transition
        x-cloak
        @click.outside="masAbierto = false"
        class="absolute bottom-full right-2 mb-20 w-56 card overflow-hidden py-1"
    >
        @if ($esAdmin)
            <x-dropdown-link :href="route('admin.fichajes.index')">Fichajes</x-dropdown-link>
            <x-dropdown-link :href="route('admin.herramientas.index')">Configuración</x-dropdown-link>
            <x-dropdown-link :href="route('documentacion.index')">Documentación</x-dropdown-link>
        @endif
        <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
        <form method="POST" action="{{ route('logout') }}" data-logout-form>
            @csrf
            <x-dropdown-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                Cerrar sesión
            </x-dropdown-link>
        </form>
    </div>

    <div class="grid {{ $esAdmin ? 'grid-cols-6' : 'grid-cols-5' }} h-16">
        @if ($esAdmin)
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('admin.dashboard') ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                </svg>
                <span class="text-[10px] font-medium">Panel</span>
            </a>
            <a href="{{ route('admin.agenda.index') }}" class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('admin.agenda.*') ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V11.25a2.25 2.25 0 012.25-2.25h13.5a2.25 2.25 0 012.25 2.25v7.5" />
                </svg>
                <span class="text-[10px] font-medium">Agenda</span>
            </a>
            <a href="{{ route('admin.tareas.index') }}" class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('admin.tareas.*') ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M5.25 4.5h13.5A2.25 2.25 0 0121 6.75v12A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75v-12A2.25 2.25 0 015.25 4.5z" />
                </svg>
                <span class="text-[10px] font-medium">Tareas</span>
            </a>
            <a href="{{ route('admin.clientes.index') }}" class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('admin.clientes.*') ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3.75h15v16.5h-15V3.75zM9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15" />
                </svg>
                <span class="text-[10px] font-medium">Clientes</span>
            </a>
            <a href="{{ route('admin.operarios.index') }}" class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('admin.operarios.*') ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <span class="text-[10px] font-medium">Operarios</span>
            </a>
        @else
            <a href="{{ route('operario.tareas.index') }}" class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('operario.tareas.index') ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                </svg>
                <span class="text-[10px] font-medium">Hoy</span>
            </a>
            <a href="{{ route('operario.tareas.historial') }}" class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('operario.tareas.historial') ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
                <span class="text-[10px] font-medium">Historial</span>
            </a>
            <a href="{{ route('operario.fichajes.index') }}" class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('operario.fichajes.index') ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-[10px] font-medium">Fichar</span>
            </a>
            <a href="{{ route('documentacion.index') }}" class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('documentacion.*') ? 'text-brand-700 dark:text-brand-400' : 'text-slate-500 dark:text-slate-400' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25a8.987 8.987 0 00-3-.512c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                </svg>
                <span class="text-[10px] font-medium">Docs</span>
            </a>
        @endif

        <button type="button" @click="masAbierto = ! masAbierto" class="flex flex-col items-center justify-center gap-0.5 text-slate-500 dark:text-slate-400">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 12.75a.75.75 0 100-1.5.75.75 0 000 1.5zM12 12.75a.75.75 0 100-1.5.75.75 0 000 1.5zm3.75 0a.75.75 0 100-1.5.75.75 0 000 1.5z" />
            </svg>
            <span class="text-[10px] font-medium">Más</span>
        </button>
    </div>
</nav>
