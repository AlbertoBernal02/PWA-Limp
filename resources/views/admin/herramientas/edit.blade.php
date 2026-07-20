<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Editar herramienta</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('admin.herramientas.update', $herramienta) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="w-full" :value="old('nombre', $herramienta->nombre)" required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cantidad" value="Cantidad en almacén" />
                        <x-text-input id="cantidad" name="cantidad" type="number" min="0" class="w-full" :value="old('cantidad', $herramienta->cantidad)" required />
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Actualiza este número cuando compres más o se gaste/rompa alguna.</p>
                        <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Ubicación en el almacén (opcional)" />
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-2">
                            Puede estar repartida en varios huecos a la vez. También puedes asignarla luego desde el
                            <a href="{{ route('admin.almacen.index') }}" class="text-brand-700 dark:text-brand-400 hover:underline">mapa del almacén</a>.
                        </p>
                        @php $huecosSeleccionados = collect(old('huecos', $herramienta->huecos->pluck('id'))); @endphp
                        @if ($huecos->isEmpty())
                            <p class="text-sm text-slate-400 dark:text-slate-500">Todavía no has creado ningún hueco.</p>
                        @else
                            <div class="flex flex-wrap gap-3">
                                @foreach ($huecos as $hueco)
                                    <label class="flex items-center gap-1.5 text-sm text-slate-700 dark:text-slate-300">
                                        <input type="checkbox" name="huecos[]" value="{{ $hueco->id }}" @checked($huecosSeleccionados->contains($hueco->id))>
                                        {{ $hueco->codigo() }}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        <x-input-error :messages="$errors->get('huecos')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Trabajos que usan esta herramienta (opcional)" />
                        @php $trabajosSeleccionados = collect(old('trabajos', $herramienta->trabajos->pluck('id'))); @endphp
                        @if ($trabajos->isEmpty())
                            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">
                                Todavía no has creado ningún <a href="{{ route('admin.trabajos.create') }}" class="text-brand-700 dark:text-brand-400 hover:underline">tipo de trabajo</a>.
                            </p>
                        @else
                            <div class="flex flex-wrap gap-3 mt-2">
                                @foreach ($trabajos as $trabajo)
                                    <label class="flex items-center gap-1.5 text-sm text-slate-700 dark:text-slate-300">
                                        <input type="checkbox" name="trabajos[]" value="{{ $trabajo->id }}" @checked($trabajosSeleccionados->contains($trabajo->id))>
                                        {{ $trabajo->nombre }}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        <x-input-error :messages="$errors->get('trabajos')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end pt-2 gap-4">
                        <a href="{{ route('admin.herramientas.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar cambios</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
