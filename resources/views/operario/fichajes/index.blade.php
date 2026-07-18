<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Fichar</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="card p-6 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    @if ($enDescanso)
                        En descanso
                    @elseif ($fichajesHoy->isEmpty())
                        Todavía no has fichado hoy
                    @elseif ($fichajesHoy->last()->tipo === 'salida')
                        Jornada de hoy finalizada
                    @else
                        Fichado desde las {{ $fichajesHoy->last()->fichado_en->format('H:i') }}
                    @endif
                </p>

                <div class="flex items-center justify-center gap-3 mt-4">
                    @foreach ($siguientes as $accion)
                        <form method="POST" action="{{ route('operario.fichajes.store') }}">
                            @csrf
                            <input type="hidden" name="tipo" value="{{ $accion }}">
                            <button type="submit" class="{{ $accion === 'salida' ? 'btn-danger' : 'btn-primary' }} px-6 py-3 text-base">
                                {{ \App\Support\Jornada::etiqueta($accion) }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="stat-card">
                    <div class="stat-icon bg-brand-50 dark:bg-brand-500/15 text-brand-700 dark:text-brand-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-slate-800 dark:text-slate-100">{{ \App\Support\Jornada::formatoHoras($segundosHoy) }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Hoy</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-slate-800 dark:text-slate-100">{{ \App\Support\Jornada::formatoHoras($segundosSemana) }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Esta semana</p>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Horas de esta semana</h3>
                <x-grafico-horas-semana :dias="$diasSemana" />
            </div>

            @if ($fichajesHoy->isNotEmpty())
                <div class="card p-5">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Fichajes de hoy</h3>
                    <ul class="space-y-2">
                        @foreach ($fichajesHoy as $fichaje)
                            <li class="flex justify-between text-sm">
                                <span class="text-slate-600 dark:text-slate-300">{{ \App\Support\Jornada::etiqueta($fichaje->tipo) }}</span>
                                <span class="text-slate-500 dark:text-slate-400">{{ $fichaje->fichado_en->format('H:i') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
