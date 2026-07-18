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
                        <x-input-label for="hueco_id" value="Ubicación en el almacén (opcional)" />
                        <select id="hueco_id" name="hueco_id" class="field-input w-full">
                            <option value="">Sin ubicación</option>
                            @foreach ($huecos as $hueco)
                                <option value="{{ $hueco->id }}" @selected(old('hueco_id', $herramienta->hueco_id) == $hueco->id)>{{ $hueco->codigo() }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            También puedes asignarla luego desde el <a href="{{ route('admin.almacen.index') }}" class="text-brand-700 dark:text-brand-400 hover:underline">mapa del almacén</a>.
                        </p>
                        <x-input-error :messages="$errors->get('hueco_id')" class="mt-2" />
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
