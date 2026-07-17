@php
    $total = $tareas->count();
    $hechas = $tareas->where('estado', 'hecha')->count();
    $porcentaje = $total > 0 ? round(($hechas / $total) * 100) : 0;
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
                                @if ($tarea->fotoEvidenciaUrl())
                                    <a href="{{ $tarea->fotoEvidenciaUrl() }}" target="_blank">
                                        <img src="{{ $tarea->fotoEvidenciaUrl() }}" alt="Evidencia de la tarea" class="w-12 h-12 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                                    </a>
                                @endif
                                @if ($tarea->nota)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 text-right">{{ $tarea->nota }}</p>
                                @endif
                            @else
                                <form method="POST" action="{{ route('operario.tareas.marcar-hecha', $tarea) }}" enctype="multipart/form-data" class="flex flex-col items-end gap-2 w-full">
                                    @csrf
                                    @method('PATCH')
                                    <textarea
                                        name="nota"
                                        rows="2"
                                        placeholder="Nota (opcional, ej. no estaba, dejé la llave con el vecino)"
                                        class="field-input text-xs w-full"
                                    ></textarea>
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
                    <svg class="w-12 h-12 text-brand-300 dark:text-brand-500/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-slate-500 dark:text-slate-400">No tienes tareas asignadas para hoy. ¡Disfruta el día!</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
