<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Configuración — Mapa del almacén</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-configuracion-tabs active="almacen" />

            <div class="card p-5">
                <p class="text-base text-slate-700 dark:text-slate-300 mb-4">
                    Esto es el <strong>plano de tu almacén</strong>, como si fuera una estantería de verdad. Cada
                    casilla es un <strong>hueco</strong> (un estante, un cajón, un armario...) y tiene un nombre
                    corto, como <strong>A1</strong> o <strong>B2</strong>.
                </p>

                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="shrink-0 w-8 h-8 rounded-full bg-brand-600 text-white font-bold flex items-center justify-center">1</span>
                        <p class="text-base text-slate-700 dark:text-slate-300 pt-0.5">
                            Pulsa <strong>"+ Añadir fila"</strong> o <strong>"+ Añadir columna"</strong> para crear los huecos de tu almacén.
                        </p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="shrink-0 w-8 h-8 rounded-full bg-brand-600 text-white font-bold flex items-center justify-center">2</span>
                        <p class="text-base text-slate-700 dark:text-slate-300 pt-0.5">
                            Toca un hueco (por ejemplo, "A1") y marca con un tick qué herramientas y qué trabajos guardas ahí.
                        </p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="shrink-0 w-8 h-8 rounded-full bg-brand-600 text-white font-bold flex items-center justify-center">3</span>
                        <p class="text-base text-slate-700 dark:text-slate-300 pt-0.5">
                            Si te equivocas, puedes borrar un hueco, una fila o una columna entera. No se pierde nada:
                            lo que tuviera guardado solo se queda sin ese hueco.
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 text-sm text-slate-500 dark:text-slate-400">
                    💡 La misma herramienta —o el mismo trabajo— puede estar guardada en varios huecos a la vez.
                </div>

                <div class="hidden lg:flex items-start gap-2 mt-3 text-sm text-slate-500 dark:text-slate-400">
                    <span>🖱️</span>
                    <p>Truco para ordenador: puedes arrastrar una tarjeta de la derecha y soltarla encima de un hueco.</p>
                </div>
            </div>

            @if ($trabajos->isNotEmpty())
                <div class="card p-5">
                    <p class="text-base text-slate-700 dark:text-slate-300 mb-3">
                        Toca un trabajo para ver qué herramientas suele llevar:
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($trabajos as $trabajo)
                            <button type="button"
                                class="badge bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400 text-sm px-3 py-1.5 hover:bg-purple-200 dark:hover:bg-purple-500/30 transition"
                                @click="$dispatch('open-modal', 'ver-trabajo-{{ $trabajo->id }}')">
                                {{ $trabajo->nombre }}
                            </button>

                            <x-modal :name="'ver-trabajo-'.$trabajo->id">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">{{ $trabajo->nombre }}</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Herramientas que suele llevar este trabajo.</p>

                                    @if ($trabajo->herramientas->isEmpty())
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            Todavía no tiene ninguna herramienta típica asignada.
                                        </p>
                                    @else
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($trabajo->herramientas as $herramienta)
                                                <span class="badge bg-brand-100 dark:bg-brand-500/20 text-brand-700 dark:text-brand-400">{{ $herramienta->nombre }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between gap-3 pt-6">
                                        <a href="{{ route('admin.trabajos.edit', $trabajo) }}" class="text-sm text-brand-700 dark:text-brand-400 hover:underline">
                                            Cambiar sus herramientas
                                        </a>
                                        <button type="button" class="btn-secondary" @click="show = false">Cerrar</button>
                                    </div>
                                </div>
                            </x-modal>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.almacen.filas.store') }}">
                    @csrf
                    <button type="submit" class="btn-secondary text-sm">+ Añadir fila</button>
                </form>
                <form method="POST" action="{{ route('admin.almacen.columnas.store') }}">
                    @csrf
                    <button type="submit" class="btn-secondary text-sm">+ Añadir columna</button>
                </form>
            </div>

            <div class="flex gap-6 items-start">
                <div class="flex-1 min-w-0">
                    @if ($filas === 0 || $columnas === 0)
                        <div class="empty-state">
                            <p class="text-slate-500 dark:text-slate-400">Todavía no has creado ningún hueco. Añade una fila o columna para empezar.</p>
                        </div>
                    @else
                        <div class="rounded-xl border-2 border-amber-200/70 dark:border-slate-700 bg-gradient-to-b from-amber-50/60 to-amber-100/30 dark:from-slate-800/60 dark:to-slate-800/20 p-4 overflow-x-auto">
                            <div class="inline-block min-w-full">
                                {{-- Cabecera de columnas: el número de "hueco" dentro de cada balda --}}
                                <div class="flex gap-3 pl-12 mb-2">
                                    @for ($columna = 1; $columna <= $columnas; $columna++)
                                        <div class="w-32 shrink-0 text-center">
                                            <p class="text-xs font-semibold text-amber-700/70 dark:text-slate-400">Hueco {{ $columna }}</p>
                                            <form method="POST" action="{{ route('admin.almacen.columnas.destroy', $columna) }}"
                                                onsubmit="return confirm('¿Eliminar la columna {{ $columna }} entera? Se eliminarán todos sus huecos; lo que tuvieran quedará sin ese hueco.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-600 hover:underline mt-0.5">
                                                    Eliminar columna
                                                </button>
                                            </form>
                                        </div>
                                    @endfor
                                </div>

                                @for ($fila = 1; $fila <= $filas; $fila++)
                                    {{-- Cada fila es una "balda" de la estantería, separada por una línea que simula el estante --}}
                                    <div class="flex items-stretch gap-3 pt-3 pb-4 {{ $fila > 1 ? 'border-t-4 border-amber-300/60 dark:border-slate-600' : '' }}">
                                        <div class="w-9 shrink-0 flex flex-col items-center justify-center gap-1">
                                            <span class="w-8 h-8 rounded-full bg-amber-200/70 dark:bg-slate-700 text-amber-800 dark:text-slate-300 text-sm font-bold flex items-center justify-center">
                                                {{ chr(64 + $fila) }}
                                            </span>
                                            <form method="POST" action="{{ route('admin.almacen.filas.destroy', $fila) }}"
                                                onsubmit="return confirm('¿Eliminar la fila {{ chr(64 + $fila) }} entera? Se eliminarán todos sus huecos; lo que tuvieran quedará sin ese hueco.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Eliminar fila {{ chr(64 + $fila) }}" class="text-slate-400 hover:text-red-500 dark:text-slate-500 dark:hover:text-red-400">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="flex gap-3">
                                            @for ($columna = 1; $columna <= $columnas; $columna++)
                                                @php $hueco = $huecos->get($fila.'-'.$columna); @endphp

                                                @if ($hueco)
                                                    <button type="button"
                                                        data-hueco-drop="{{ $hueco->id }}"
                                                        class="w-32 min-h-[6rem] shrink-0 text-left p-2.5 rounded-md border-2 transition shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] hover:-translate-y-0.5 hover:shadow-md
                                                            {{ $hueco->herramientas->isEmpty() && $hueco->trabajos->isEmpty()
                                                                ? 'border-dashed border-slate-300 dark:border-slate-600 bg-white/60 dark:bg-slate-900/30 hover:border-brand-400'
                                                                : 'border-brand-300 dark:border-brand-500/40 bg-brand-50/80 dark:bg-brand-500/10 hover:border-brand-500' }}"
                                                        @click="$dispatch('open-modal', 'hueco-{{ $hueco->id }}')">
                                                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $hueco->codigo() }}</p>
                                                        @if ($hueco->herramientas->isEmpty() && $hueco->trabajos->isEmpty())
                                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-2 flex items-center gap-1">
                                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c0-.399.078-.794.229-1.164L5.606 4.6a2.25 2.25 0 012.088-1.4h8.612a2.25 2.25 0 012.088 1.4l1.627 4.012c.151.37.229.765.229 1.164v7.474a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V9.776z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5M9 13.5h6" />
                                                                </svg>
                                                                Vacío
                                                            </p>
                                                        @else
                                                            <div class="flex flex-wrap gap-1 mt-1.5">
                                                                @foreach ($hueco->trabajos as $trabajo)
                                                                    <span class="badge bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-400">{{ $trabajo->nombre }}</span>
                                                                @endforeach
                                                                @foreach ($hueco->herramientas as $herramienta)
                                                                    <span class="badge bg-brand-100 dark:bg-brand-500/20 text-brand-700 dark:text-brand-400">{{ $herramienta->nombre }}</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </button>

                                                    <x-modal :name="'hueco-'.$hueco->id">
                                                        <div class="p-6">
                                                            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">Hueco {{ $hueco->codigo() }}</h3>
                                                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Marca qué hay guardado aquí.</p>

                                                            <form method="POST" action="{{ route('admin.almacen.huecos.asignar', $hueco) }}" class="space-y-4">
                                                                @csrf

                                                                @if ($trabajos->isNotEmpty())
                                                                    <div>
                                                                        <x-input-label value="Trabajos" />
                                                                        <div class="space-y-2 max-h-40 overflow-y-auto mt-1">
                                                                            @foreach ($trabajos as $trabajo)
                                                                                @php $otrosHuecosTrabajo = $trabajo->huecos->reject(fn ($h) => $h->id === $hueco->id); @endphp
                                                                                <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                                                                    <input type="checkbox" name="trabajos[]" value="{{ $trabajo->id }}"
                                                                                        {{ $trabajo->huecos->contains($hueco->id) ? 'checked' : '' }}>
                                                                                    {{ $trabajo->nombre }}
                                                                                    @if ($otrosHuecosTrabajo->isNotEmpty())
                                                                                        <span class="text-xs text-slate-400 dark:text-slate-500">(también en {{ $otrosHuecosTrabajo->map(fn ($h) => $h->codigo())->implode(', ') }})</span>
                                                                                    @endif
                                                                                </label>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if ($herramientas->isEmpty())
                                                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                                                        No hay herramientas creadas todavía.
                                                                        <a href="{{ route('admin.herramientas.create') }}" class="text-brand-700 dark:text-brand-400 hover:underline">Crear una</a>.
                                                                    </p>
                                                                @else
                                                                    <div>
                                                                        <x-input-label value="Herramientas" />
                                                                        <div class="space-y-2 max-h-56 overflow-y-auto mt-1">
                                                                            @foreach ($herramientas as $herramienta)
                                                                                @php $otrosHuecos = $herramienta->huecos->reject(fn ($h) => $h->id === $hueco->id); @endphp
                                                                                <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                                                                    <input type="checkbox" name="herramientas[]" value="{{ $herramienta->id }}"
                                                                                        {{ $herramienta->huecos->contains($hueco->id) ? 'checked' : '' }}>
                                                                                    {{ $herramienta->nombre }}
                                                                                    @if ($otrosHuecos->isNotEmpty())
                                                                                        <span class="text-xs text-slate-400 dark:text-slate-500">(también en {{ $otrosHuecos->map(fn ($h) => $h->codigo())->implode(', ') }})</span>
                                                                                    @endif
                                                                                </label>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <div class="flex items-center justify-between gap-3 pt-2">
                                                                    <button type="submit" form="eliminar-hueco-{{ $hueco->id }}" class="text-sm text-red-600 hover:underline">
                                                                        Eliminar hueco
                                                                    </button>
                                                                    <div class="flex items-center gap-3">
                                                                        <button type="button" class="btn-secondary" @click="show = false">Cancelar</button>
                                                                        <button type="submit" class="btn-primary">Guardar</button>
                                                                    </div>
                                                                </div>
                                                            </form>

                                                            <form id="eliminar-hueco-{{ $hueco->id }}" method="POST" action="{{ route('admin.almacen.huecos.destroy', $hueco) }}"
                                                                onsubmit="return confirm('¿Eliminar el hueco {{ $hueco->codigo() }}? Lo que tuviera guardado se quedará sin ese hueco.');">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </div>
                                                    </x-modal>
                                                @else
                                                    <form method="POST" action="{{ route('admin.almacen.huecos.store') }}">
                                                        @csrf
                                                        <input type="hidden" name="fila" value="{{ $fila }}">
                                                        <input type="hidden" name="columna" value="{{ $columna }}">
                                                        <button type="submit"
                                                            class="w-32 min-h-[6rem] shrink-0 text-left p-2.5 rounded-md border-2 border-dotted border-slate-300 dark:border-slate-600 text-slate-400 dark:text-slate-500 hover:border-brand-400 hover:text-brand-600 dark:hover:text-brand-400 transition"
                                                            title="Crear el hueco {{ chr(64 + $fila) }}{{ $columna }}">
                                                            <span class="text-xs font-bold">{{ chr(64 + $fila) }}{{ $columna }}</span>
                                                            <span class="block text-xs mt-2">+ Crear hueco</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Paneles de tarjetas arrastrables: solo en escritorio. En móvil no hay gesto de
                     "arrastrar" fiable, así que ahí se sigue usando únicamente el modal de cada hueco. --}}
                @if ($filas > 0 && $columnas > 0)
                    @if ($trabajos->isNotEmpty())
                        <div class="hidden lg:block w-56 shrink-0 sticky top-4">
                            <div class="card p-4">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Trabajos</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Arrastra una tarjeta a un hueco para marcarlo con ese trabajo, o toca el nombre para ver sus herramientas.</p>
                                <div class="space-y-2">
                                    @foreach ($trabajos as $trabajo)
                                        <div draggable="true" data-trabajo-id="{{ $trabajo->id }}"
                                            class="cursor-grab active:cursor-grabbing rounded-lg border border-purple-200 dark:border-purple-500/40 bg-purple-50/60 dark:bg-purple-500/10 px-3 py-2 text-sm text-purple-800 dark:text-purple-300 hover:border-purple-400 transition">
                                            <button type="button" class="hover:underline text-left" @click="$dispatch('open-modal', 'ver-trabajo-{{ $trabajo->id }}')">
                                                {{ $trabajo->nombre }}
                                            </button>
                                            @if ($trabajo->huecos->isNotEmpty())
                                                <span class="block text-xs text-purple-500/70 dark:text-purple-400/70 mt-0.5">{{ $trabajo->huecos->map(fn ($h) => $h->codigo())->implode(', ') }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($herramientas->isNotEmpty())
                        <div class="hidden lg:block w-56 shrink-0 sticky top-4">
                            <div class="card p-4">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Herramientas</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Arrastra una tarjeta a un hueco para guardarla ahí.</p>
                                <div class="space-y-2">
                                    @foreach ($herramientas as $herramienta)
                                        <div draggable="true" data-herramienta-id="{{ $herramienta->id }}"
                                            class="cursor-grab active:cursor-grabbing rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:border-brand-400 dark:hover:border-brand-500 transition">
                                            {{ $herramienta->nombre }}
                                            @if ($herramienta->huecos->isNotEmpty())
                                                <span class="block text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $herramienta->huecos->map(fn ($h) => $h->codigo())->implode(', ') }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
