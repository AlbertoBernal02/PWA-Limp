@php
    $total = $tareas->count();
    $hechas = $tareas->where('estado', 'hecha')->count();
    $porcentaje = $total > 0 ? round(($hechas / $total) * 100) : 0;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h2>
            <p class="text-sm text-slate-500 capitalize">{{ now()->translatedFormat('l, d \d\e F') }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($total > 0)
                <div class="card p-5">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-medium text-slate-700">Progreso de hoy</span>
                        <span class="text-slate-500">{{ $hechas }} de {{ $total }} completadas</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5">
                        <div class="bg-brand-500 h-2.5 rounded-full transition-all" style="width: {{ $porcentaje }}%"></div>
                    </div>
                </div>
            @endif

            @forelse ($tareas as $tarea)
                <div class="card p-5 {{ $tarea->estado === 'hecha' ? 'opacity-60' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex gap-3">
                            <div class="shrink-0 w-11 h-11 rounded-lg bg-brand-50 text-brand-700 flex flex-col items-center justify-center text-xs font-semibold">
                                {{ $tarea->hora_estimada ? \Illuminate\Support\Carbon::parse($tarea->hora_estimada)->format('H:i') : '—' }}
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-slate-800">{{ $tarea->cliente->nombre }}</h3>
                                <p class="text-sm text-slate-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    {{ $tarea->cliente->direccion }}
                                </p>
                                @if ($tarea->descripcion)
                                    <p class="text-sm text-slate-600 mt-2">{{ $tarea->descripcion }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if ($tarea->estado === 'hecha')
                                <span class="badge-done">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Hecha
                                </span>
                            @else
                                <form method="POST" action="{{ route('operario.tareas.marcar-hecha', $tarea) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-primary">
                                        Marcar hecha
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <svg class="w-12 h-12 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-slate-500">No tienes tareas asignadas para hoy. ¡Disfruta el día!</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
