@php
    $mesAnterior = $mes->copy()->subMonth()->format('Y-m');
    $mesSiguiente = $mes->copy()->addMonth()->format('Y-m');
    $mesActual = $mes->format('Y-m');
    $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Agenda</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex items-center justify-between gap-2">
                <a href="{{ route('admin.agenda.index', ['mes' => $mesAnterior]) }}" class="btn-secondary">← Anterior</a>
                <div class="text-center">
                    <p class="text-lg font-semibold text-slate-800 dark:text-slate-100 capitalize">{{ $mes->translatedFormat('F Y') }}</p>
                    @if ($mesActual !== now()->format('Y-m'))
                        <a href="{{ route('admin.agenda.index') }}" class="text-xs text-brand-700 dark:text-brand-400 hover:underline">Ir a hoy</a>
                    @endif
                </div>
                <a href="{{ route('admin.agenda.index', ['mes' => $mesSiguiente]) }}" class="btn-secondary">Siguiente →</a>
            </div>

            {{-- min-w-0: sin esto, la rejilla puede forzar overflow horizontal del
                 body y descolocar la barra inferior/el chat flotante en móvil
                 (ya pasó antes con tablas anchas; ver admin/almacen para el mismo patrón). --}}
            <div class="min-w-0 card p-3 sm:p-4" x-data="{ diaSeleccionado: null }">
                <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-1 sm:mb-2">
                    @foreach ($diasSemana as $nombreDia)
                        <div class="text-center text-[10px] sm:text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase">
                            {{ $nombreDia }}
                        </div>
                    @endforeach
                </div>

                @foreach ($semanas as $semana)
                    <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-1 sm:mb-2">
                        @foreach ($semana as $dia)
                            @php
                                $fechaStr = $dia['fecha']->toDateString();
                                $tareasDia = $tareasPorDia->get($fechaStr, collect());
                                $notasDia = $notasPorDia->get($fechaStr, collect());
                                $pendientes = $tareasDia->where('estado', 'pendiente')->count();
                                $hechas = $tareasDia->where('estado', 'hecha')->count();
                                $numNotas = $notasDia->count();
                                $esHoy = $dia['fecha']->isToday();
                            @endphp

                            @if ($dia['esDelMes'])
                                <button
                                    type="button"
                                    @click="diaSeleccionado = '{{ $fechaStr }}'; $dispatch('open-modal', 'dia-agenda')"
                                    class="h-14 sm:h-20 rounded-lg border p-1 sm:p-2 flex flex-col items-center sm:items-start gap-1 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition
                                        {{ $esHoy ? 'ring-2 ring-brand-500 border-transparent' : 'border-slate-200 dark:border-slate-700' }}"
                                >
                                    <span class="text-[11px] sm:text-sm font-medium {{ $esHoy ? 'text-brand-700 dark:text-brand-400' : 'text-slate-700 dark:text-slate-200' }}">
                                        {{ $dia['fecha']->day }}
                                    </span>
                                    <div class="flex flex-wrap gap-0.5 justify-center sm:justify-start">
                                        @if ($pendientes > 0)
                                            <span class="w-3.5 h-3.5 sm:w-auto sm:h-auto sm:px-1 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-[8px] sm:text-[9px] leading-none flex items-center justify-center" title="{{ $pendientes }} tarea(s) pendiente(s)">{{ $pendientes }}</span>
                                        @endif
                                        @if ($hechas > 0)
                                            <span class="w-3.5 h-3.5 sm:w-auto sm:h-auto sm:px-1 rounded-full bg-accent-100 dark:bg-accent-500/20 text-accent-700 dark:text-accent-400 text-[8px] sm:text-[9px] leading-none flex items-center justify-center" title="{{ $hechas }} tarea(s) hecha(s)">{{ $hechas }}</span>
                                        @endif
                                        @if ($numNotas > 0)
                                            <span class="w-3.5 h-3.5 sm:w-auto sm:h-auto sm:px-1 rounded-full bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300 text-[8px] sm:text-[9px] leading-none flex items-center justify-center" title="{{ $numNotas }} nota(s)">{{ $numNotas }}</span>
                                        @endif
                                    </div>
                                </button>
                            @else
                                <div class="h-14 sm:h-20 rounded-lg p-1 sm:p-2 text-[11px] sm:text-sm text-slate-300 dark:text-slate-600">
                                    {{ $dia['fecha']->day }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach

                {{-- Modal único compartido por todos los días del mes: evitar 42
                     <x-modal> independientes (cada uno con su propio x-data y
                     listeners globales de teclado/foco) es innecesariamente pesado. --}}
                <x-modal name="dia-agenda" max-width="lg">
                    <div class="p-6 max-h-[80vh] overflow-y-auto">
                        @foreach ($semanas as $semana)
                            @foreach ($semana as $dia)
                                @continue (! $dia['esDelMes'])
                                @php
                                    $fechaStr = $dia['fecha']->toDateString();
                                    $tareasDia = $tareasPorDia->get($fechaStr, collect());
                                    $notasDia = $notasPorDia->get($fechaStr, collect());
                                @endphp
                                <div x-show="diaSeleccionado === '{{ $fechaStr }}'" x-cloak class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 capitalize">
                                            {{ $dia['fecha']->translatedFormat('l, d \d\e F') }}
                                        </h3>
                                        <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300" @click="show = false" aria-label="Cerrar">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div>
                                        <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">Tareas</h4>
                                        @forelse ($tareasDia as $tarea)
                                            <a href="{{ route('admin.tareas.edit', $tarea) }}" class="flex items-center justify-between gap-2 py-2 border-b border-slate-100 dark:border-slate-700 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/40 -mx-2 px-2 rounded">
                                                <span class="text-sm text-slate-700 dark:text-slate-300">
                                                    @if ($tarea->hora_estimada)
                                                        <span class="text-slate-400 dark:text-slate-500">{{ \Illuminate\Support\Carbon::parse($tarea->hora_estimada)->format('H:i') }}</span>
                                                    @endif
                                                    {{ $tarea->cliente->nombre }} — {{ $tarea->operario->name }}
                                                </span>
                                                <span class="{{ $tarea->estado === 'hecha' ? 'badge-done' : 'badge-pending' }}">
                                                    {{ $tarea->estado === 'hecha' ? 'Hecha' : 'Pendiente' }}
                                                </span>
                                            </a>
                                        @empty
                                            <p class="text-sm text-slate-400 dark:text-slate-500">Sin tareas este día.</p>
                                        @endforelse
                                    </div>

                                    <div>
                                        <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">Notas</h4>
                                        @forelse ($notasDia as $nota)
                                            <div class="flex items-start justify-between gap-2 py-2 border-b border-slate-100 dark:border-slate-700 last:border-0">
                                                <div>
                                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $nota->titulo }}</p>
                                                    @if ($nota->descripcion)
                                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $nota->descripcion }}</p>
                                                    @endif
                                                    @if ($nota->operario)
                                                        <p class="text-xs text-brand-700 dark:text-brand-400">{{ $nota->operario->name }}</p>
                                                    @endif
                                                </div>
                                                <form method="POST" action="{{ route('admin.agenda.notas.destroy', ['nota' => $nota, 'mes' => $mesActual]) }}" onsubmit="return confirm('¿Eliminar esta nota?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                                                </form>
                                            </div>
                                        @empty
                                            <p class="text-sm text-slate-400 dark:text-slate-500">Sin notas este día.</p>
                                        @endforelse
                                    </div>

                                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                                        <a href="{{ route('admin.tareas.create', ['fecha' => $fechaStr, 'volver_a_mes' => $mesActual]) }}" class="btn-secondary">
                                            + Añadir tarea
                                        </a>
                                    </div>

                                    <form method="POST" action="{{ route('admin.agenda.notas.store') }}" class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-700">
                                        @csrf
                                        <input type="hidden" name="fecha" value="{{ $fechaStr }}">
                                        <input type="hidden" name="mes" value="{{ $mesActual }}">
                                        <x-input-label value="+ Añadir nota" />
                                        <input type="text" name="titulo" maxlength="255" placeholder="Título" class="field-input" required>
                                        <textarea name="descripcion" rows="2" maxlength="2000" placeholder="Descripción (opcional)" class="field-input"></textarea>
                                        <select name="operario_id" class="field-input">
                                            <option value="">Sin operario asignado</option>
                                            @foreach ($operarios as $operario)
                                                <option value="{{ $operario->id }}">{{ $operario->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-primary w-full">Guardar nota</button>
                                    </form>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </x-modal>
            </div>
        </div>
    </div>
</x-app-layout>
