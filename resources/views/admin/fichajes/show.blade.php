@php
    $semanaAnterior = $inicioSemana->copy()->subWeek()->toDateString();
    $semanaSiguiente = $inicioSemana->copy()->addWeek()->toDateString();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">{{ $operario->name }}</h2>
            <a href="{{ route('admin.fichajes.index') }}" class="btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="card p-5 flex items-center justify-between">
                <a href="{{ route('admin.fichajes.show', ['operario' => $operario, 'semana' => $semanaAnterior]) }}" class="btn-secondary">← Semana anterior</a>
                <div class="text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $inicioSemana->format('d/m') }} — {{ $finSemana->format('d/m/Y') }}</p>
                    <p class="text-2xl font-semibold text-slate-800 dark:text-slate-100">{{ \App\Support\Jornada::formatoHoras($segundosSemana) }}</p>
                </div>
                <a href="{{ route('admin.fichajes.show', ['operario' => $operario, 'semana' => $semanaSiguiente]) }}" class="btn-secondary">Semana siguiente →</a>
            </div>

            <div class="card p-5">
                <x-grafico-horas-semana :dias="$diasSemana" />
            </div>

            <div class="card p-5">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Informe mensual (nómina)</h3>
                <x-exportar-informe :accion-base="route('admin.fichajes.informe', $operario)" :mes-por-defecto="$inicioSemana->format('Y-m')" />
            </div>

            @forelse ($porDia as $dia => $fichajesDelDia)
                <div class="card p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-slate-800 dark:text-slate-100 capitalize">
                            {{ \Illuminate\Support\Carbon::parse($dia)->translatedFormat('l, d \d\e F') }}
                        </h3>
                        <span class="text-sm text-slate-500 dark:text-slate-400">
                            {{ \App\Support\Jornada::formatoHoras(\App\Support\Jornada::segundosTrabajadosHasta($fichajesDelDia)) }}
                        </span>
                    </div>

                    <ul class="space-y-2">
                        @foreach ($fichajesDelDia as $fichaje)
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-slate-600 dark:text-slate-300">
                                    {{ \App\Support\Jornada::etiqueta($fichaje->tipo) }}
                                    @if ($fichaje->creado_por_admin)
                                        <span class="text-xs text-slate-400 dark:text-slate-500">(manual)</span>
                                    @endif
                                </span>
                                <span class="flex items-center gap-3">
                                    <span class="text-slate-500 dark:text-slate-400">{{ $fichaje->fichado_en->format('H:i') }}</span>
                                    <form method="POST" action="{{ route('admin.fichajes.destroy', $fichaje) }}" onsubmit="return confirm('¿Eliminar este fichaje?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs">Eliminar</button>
                                    </form>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <div class="empty-state">
                    <p class="text-slate-500 dark:text-slate-400">Sin fichajes esta semana.</p>
                </div>
            @endforelse

            <div class="card p-5">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Añadir fichaje manual</h3>
                <form method="POST" action="{{ route('admin.fichajes.store', $operario) }}" class="flex flex-wrap items-end gap-4">
                    @csrf
                    <div>
                        <x-input-label for="tipo" value="Tipo" />
                        <select id="tipo" name="tipo" class="field-input" required>
                            <option value="entrada">Entrada</option>
                            <option value="inicio_descanso">Inicio descanso</option>
                            <option value="fin_descanso">Fin descanso</option>
                            <option value="salida">Salida</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="fichado_en" value="Fecha y hora" />
                        <input type="datetime-local" id="fichado_en" name="fichado_en" class="field-input" required>
                    </div>
                    <button type="submit" class="btn-primary">Añadir</button>
                </form>
                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                <x-input-error :messages="$errors->get('fichado_en')" class="mt-2" />
            </div>
        </div>
    </div>
</x-app-layout>
