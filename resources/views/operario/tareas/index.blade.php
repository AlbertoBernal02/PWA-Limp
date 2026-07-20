@php
    $total = $tareas->count();
    $hechas = $tareas->where('estado', 'hecha')->count();
    $porcentaje = $total > 0 ? round(($hechas / $total) * 100) : 0;
    $pendientes = $tareas->where('estado', 'pendiente')->count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 capitalize">{{ now()->translatedFormat('l, d \d\e F') }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @unless ($estaTrabajando)
                <div class="card p-5 border-l-4 border-amber-400 dark:border-amber-500 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <p class="text-sm text-slate-700 dark:text-slate-300">
                            Todavía no has fichado la entrada — hazlo antes de marcar tareas como hechas.
                        </p>
                    </div>
                    <a href="{{ route('operario.fichajes.index') }}" class="btn-primary shrink-0">Fichar entrada</a>
                </div>
            @endunless

            @if ($total > 0)
                <div class="card p-5">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Progreso de hoy</span>
                        <span class="text-slate-500 dark:text-slate-400">{{ $hechas }} de {{ $total }} completadas</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5">
                        <div class="bg-brand-500 h-2.5 rounded-full transition-all" style="width: {{ $porcentaje }}%"></div>
                    </div>
                </div>
            @endif

            @if ($pendientes >= 3)
                <form method="POST" action="{{ route('operario.tareas.optimizar-ruta') }}">
                    @csrf
                    <button type="submit" class="btn-secondary w-full flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                        </svg>
                        Optimizar ruta de hoy por cercanía
                    </button>
                </form>
            @endif

            @forelse ($tareas as $tarea)
                <div class="card p-5 {{ $tarea->estado === 'hecha' ? 'opacity-60' : '' }}">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex gap-3">
                            @if ($tarea->cliente->fotoUrl())
                                <img src="{{ $tarea->cliente->fotoUrl() }}" alt="Foto de {{ $tarea->cliente->nombre }}" class="shrink-0 w-11 h-11 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                            @else
                                <div class="shrink-0 w-11 h-11 rounded-lg bg-brand-50 dark:bg-brand-500/15 text-brand-700 dark:text-brand-400 flex flex-col items-center justify-center text-xs font-semibold">
                                    {{ $tarea->hora_estimada ? \Illuminate\Support\Carbon::parse($tarea->hora_estimada)->format('H:i') : '—' }}
                                </div>
                            @endif
                            <div>
                                <div class="flex items-center gap-2">
                                    @if ($tarea->estado === 'pendiente' && $tarea->orden_ruta)
                                        <span class="badge bg-brand-100 dark:bg-brand-500/20 text-brand-700 dark:text-brand-400" title="Orden sugerido de la ruta de hoy">
                                            Parada {{ $tarea->orden_ruta }}
                                        </span>
                                    @endif
                                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ $tarea->cliente->nombre }}</h3>
                                    @if ($tarea->cliente->fotoUrl() && $tarea->hora_estimada)
                                        <span class="text-xs font-semibold text-brand-700 dark:text-brand-400">{{ \Illuminate\Support\Carbon::parse($tarea->hora_estimada)->format('H:i') }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    {{ $tarea->cliente->direccion }}
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($tarea->cliente->direccion) }}" target="_blank" rel="noopener" class="text-brand-700 dark:text-brand-400 font-medium hover:underline whitespace-nowrap">
                                        Cómo llegar
                                    </a>
                                </p>
                                @if ($tarea->cliente->telefono)
                                    <a href="tel:{{ preg_replace('/[^+0-9]/', '', $tarea->cliente->telefono) }}" class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1 hover:text-brand-700 dark:hover:text-brand-400">
                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a2.25 2.25 0 00-2.395.708l-.339.44a1.5 1.5 0 01-1.635.446 12.09 12.09 0 01-6.02-6.02 1.5 1.5 0 01.446-1.635l.44-.339a2.25 2.25 0 00.708-2.395L6.883 3.102a2.25 2.25 0 00-1.091-.852H4.5a2.25 2.25 0 00-2.25 2.25v1.5z" />
                                        </svg>
                                        {{ $tarea->cliente->telefono }}
                                    </a>
                                @endif
                                @if ($tarea->descripcion)
                                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-2">{{ $tarea->descripcion }}</p>
                                @endif
                                @if ($tarea->herramientas->isNotEmpty())
                                    <div class="mt-2">
                                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.187 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.052.68-.045 1.399-.45 1.951l-.313.44" />
                                            </svg>
                                            Herramientas a llevar
                                        </p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach ($tarea->herramientas as $herramienta)
                                                <span class="badge bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $herramienta->nombre }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if ($tarea->estado === 'pendiente' && $tarea->iniciada_at)
                                    <p class="text-xs text-brand-700 dark:text-brand-400 mt-2">En progreso desde las {{ $tarea->iniciada_at->format('H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 flex flex-col items-end gap-2 max-w-[14rem]">
                            @if ($tarea->estado === 'hecha')
                                <span class="badge-done">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Hecha
                                </span>
                                @if ($tarea->duracionFormateada())
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Duración: {{ $tarea->duracionFormateada() }}</span>
                                @endif
                                @if ($tarea->fotoEvidenciaUrl())
                                    <a href="{{ $tarea->fotoEvidenciaUrl() }}" target="_blank">
                                        <img src="{{ $tarea->fotoEvidenciaUrl() }}" alt="Evidencia de la tarea" class="w-12 h-12 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                                    </a>
                                @endif
                                @if ($tarea->nota)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 text-right">{{ $tarea->nota }}</p>
                                @endif
                            @elseif (! $tarea->iniciada_at)
                                <form method="POST" action="{{ route('operario.tareas.empezar', $tarea) }}" data-empezar-tarea data-turbo="false">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-secondary">
                                        Empezar
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn-primary" @click="$dispatch('open-modal', 'marcar-hecha-{{ $tarea->id }}')">
                                    Marcar hecha
                                </button>

                                <x-modal :name="'marcar-hecha-'.$tarea->id">
                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">Finalizar tarea</h3>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ $tarea->cliente->nombre }}</p>

                                        <form method="POST" action="{{ route('operario.tareas.marcar-hecha', $tarea) }}" enctype="multipart/form-data" class="space-y-4" data-marcar-hecha data-tarea-id="{{ $tarea->id }}" data-turbo="false">
                                            @csrf
                                            @method('PATCH')

                                            @if ($tarea->herramientas->isNotEmpty())
                                                <div>
                                                    <x-input-label value="¿Cuánto has gastado de cada herramienta? (opcional)" />
                                                    <div class="space-y-2 mt-2">
                                                        @foreach ($tarea->herramientas as $herramienta)
                                                            <div class="flex items-center justify-between gap-3">
                                                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $herramienta->nombre }}</span>
                                                                <input type="number" min="0" name="cantidades[{{ $herramienta->id }}]" placeholder="0" class="field-input w-24 text-sm">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <div>
                                                <x-input-label value="Nota (opcional)" />
                                                <textarea
                                                    name="nota"
                                                    rows="2"
                                                    placeholder="Ej. no estaba, dejé la llave con el vecino; o: usé 2 bayetas que no llevaba apuntadas"
                                                    class="field-input text-sm w-full mt-1"
                                                ></textarea>
                                            </div>

                                            <label class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 cursor-pointer hover:text-brand-700 dark:hover:text-brand-400">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9a3.75 3.75 0 100 7.5A3.75 3.75 0 0012 9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.344 3.071a49.52 49.52 0 015.312 0c.967.052 1.83.585 2.332 1.39l.821 1.317c.24.383.645.643 1.11.71.386.054.77.113 1.152.177 1.432.239 2.429 1.493 2.429 2.909V18a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 18V9.574c0-1.416.997-2.67 2.429-2.909.382-.064.766-.123 1.151-.178a1.56 1.56 0 001.11-.71l.822-1.316a2.942 2.942 0 012.332-1.39z" />
                                                </svg>
                                                <span class="foto-nombre">Añadir foto (opcional)</span>
                                                <input
                                                    type="file"
                                                    name="foto_evidencia"
                                                    accept="image/*"
                                                    capture="environment"
                                                    class="hidden"
                                                    onchange="this.closest('form').querySelector('.foto-nombre').textContent = this.files[0]?.name ?? 'Añadir foto (opcional)'"
                                                >
                                            </label>

                                            <div class="flex items-center justify-end gap-3 pt-2">
                                                <button type="button" class="btn-secondary" @click="show = false">Cancelar</button>
                                                <button type="submit" class="btn-primary">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <svg class="w-12 h-12 text-brand-300 dark:text-brand-500/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-slate-500 dark:text-slate-400">No tienes tareas asignadas para hoy. ¡Disfruta el día!</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
