<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Configuración — Mapa del almacén</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex gap-4 border-b border-slate-200 dark:border-slate-700 text-sm font-medium">
                <a href="{{ route('admin.herramientas.index') }}" class="px-1 py-2 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                    Herramientas
                </a>
                <a href="{{ route('admin.almacen.index') }}" class="px-1 py-2 text-brand-700 dark:text-brand-400 border-b-2 border-brand-600">
                    Mapa del almacén
                </a>
            </div>

            <div class="text-sm text-slate-500 dark:text-slate-400 space-y-1">
                <p>
                    Piensa en esto como el plano de tu almacén: cada casilla (<strong>hueco</strong>) es un
                    estante, cajón o armario real, con un código tipo <strong>A1</strong>, <strong>B2</strong>...
                </p>
                <p>
                    1) Usa los botones de abajo para crear la cuadrícula (una fila o columna nueva de huecos
                    vacíos). 2) Haz clic en un hueco para marcar, con checkboxes, qué herramientas hay guardadas
                    ahí; desde ese mismo panel puedes "Eliminar hueco". 3) También puedes borrar una fila o
                    columna entera: la ✕ junto a la letra de cada fila, o "Eliminar columna" bajo cada número. En
                    todos los casos, las herramientas que hubiera no se borran, solo quedan sin ubicación.
                </p>
            </div>

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
                                        onsubmit="return confirm('¿Eliminar la columna {{ $columna }} entera? Se eliminarán todos sus huecos; las herramientas que tuvieran quedarán sin ubicación.');">
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
                                        onsubmit="return confirm('¿Eliminar la fila {{ chr(64 + $fila) }} entera? Se eliminarán todos sus huecos; las herramientas que tuvieran quedarán sin ubicación.');">
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
                                                class="w-32 min-h-[6rem] shrink-0 text-left p-2.5 rounded-md border-2 transition shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] hover:-translate-y-0.5 hover:shadow-md
                                                    {{ $hueco->herramientas->isEmpty()
                                                        ? 'border-dashed border-slate-300 dark:border-slate-600 bg-white/60 dark:bg-slate-900/30 hover:border-brand-400'
                                                        : 'border-brand-300 dark:border-brand-500/40 bg-brand-50/80 dark:bg-brand-500/10 hover:border-brand-500' }}"
                                                @click="$dispatch('open-modal', 'hueco-{{ $hueco->id }}')">
                                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $hueco->codigo() }}</p>
                                                @if ($hueco->herramientas->isEmpty())
                                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-2 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c0-.399.078-.794.229-1.164L5.606 4.6a2.25 2.25 0 012.088-1.4h8.612a2.25 2.25 0 012.088 1.4l1.627 4.012c.151.37.229.765.229 1.164v7.474a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V9.776z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5M9 13.5h6" />
                                                        </svg>
                                                        Vacío
                                                    </p>
                                                @else
                                                    <div class="flex flex-wrap gap-1 mt-1.5">
                                                        @foreach ($hueco->herramientas as $herramienta)
                                                            <span class="badge bg-brand-100 dark:bg-brand-500/20 text-brand-700 dark:text-brand-400">{{ $herramienta->nombre }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </button>

                                            <x-modal :name="'hueco-'.$hueco->id">
                                                <div class="p-6">
                                                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">Hueco {{ $hueco->codigo() }}</h3>
                                                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Marca qué herramientas están guardadas aquí.</p>

                                                    <form method="POST" action="{{ route('admin.almacen.huecos.asignar', $hueco) }}" class="space-y-4">
                                                        @csrf

                                                        @if ($herramientas->isEmpty())
                                                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                                                No hay herramientas creadas todavía.
                                                                <a href="{{ route('admin.herramientas.create') }}" class="text-brand-700 dark:text-brand-400 hover:underline">Crear una</a>.
                                                            </p>
                                                        @else
                                                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                                                @foreach ($herramientas as $herramienta)
                                                                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                                                        <input type="checkbox" name="herramientas[]" value="{{ $herramienta->id }}"
                                                                            {{ $herramienta->hueco_id === $hueco->id ? 'checked' : '' }}>
                                                                        {{ $herramienta->nombre }}
                                                                        @if ($herramienta->hueco_id && $herramienta->hueco_id !== $hueco->id)
                                                                            <span class="text-xs text-slate-400 dark:text-slate-500">(en {{ $herramienta->hueco->codigo() }})</span>
                                                                        @endif
                                                                    </label>
                                                                @endforeach
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
                                                        onsubmit="return confirm('¿Eliminar el hueco {{ $hueco->codigo() }}? {{ $hueco->herramientas->isNotEmpty() ? 'Las herramientas que tiene guardadas se quedarán sin ubicación.' : '' }}');">
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
    </div>
</x-app-layout>
