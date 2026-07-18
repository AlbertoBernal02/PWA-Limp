<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Historial de tareas</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($tareas as $tarea)
                <div class="card p-5 {{ $tarea->estado === 'hecha' ? '' : 'opacity-60' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex gap-3">
                            @if ($tarea->cliente->fotoUrl())
                                <img src="{{ $tarea->cliente->fotoUrl() }}" alt="Foto de {{ $tarea->cliente->nombre }}" class="shrink-0 w-11 h-11 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                            @else
                                <div class="shrink-0 w-11 h-11 rounded-lg bg-brand-50 dark:bg-brand-500/15 text-brand-700 dark:text-brand-400 flex flex-col items-center justify-center text-xs font-semibold">
                                    {{ $tarea->fecha->format('d/m') }}
                                </div>
                            @endif
                            <div>
                                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ $tarea->cliente->nombre }}</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $tarea->fecha->translatedFormat('d \d\e F') }}
                                    @if ($tarea->hora_estimada)
                                        · {{ \Illuminate\Support\Carbon::parse($tarea->hora_estimada)->format('H:i') }}
                                    @endif
                                </p>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $tarea->cliente->direccion }}</p>
                                @if ($tarea->descripcion)
                                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-2">{{ $tarea->descripcion }}</p>
                                @endif
                                @if ($tarea->herramientas->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach ($tarea->herramientas as $herramienta)
                                            <span class="badge bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $herramienta->nombre }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                @if ($tarea->nota)
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 italic">"{{ $tarea->nota }}"</p>
                                @endif
                                @if ($tarea->duracionFormateada())
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Duración: {{ $tarea->duracionFormateada() }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 flex flex-col items-end gap-2">
                            @if ($tarea->estado === 'hecha')
                                <span class="badge-done">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Hecha
                                </span>
                            @else
                                <span class="badge-pending">Pendiente</span>
                            @endif
                            @if ($tarea->fotoEvidenciaUrl())
                                <a href="{{ $tarea->fotoEvidenciaUrl() }}" target="_blank">
                                    <img src="{{ $tarea->fotoEvidenciaUrl() }}" alt="Evidencia de la tarea" class="w-12 h-12 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <svg class="w-12 h-12 text-brand-300 dark:text-brand-500/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-slate-500 dark:text-slate-400">Todavía no tienes tareas pasadas.</p>
                </div>
            @endforelse

            @if ($tareas->hasPages())
                <div class="mt-4">
                    {{ $tareas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
