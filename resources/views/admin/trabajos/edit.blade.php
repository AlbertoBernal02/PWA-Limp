<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Editar trabajo</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('admin.trabajos.update', $trabajo) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="w-full" :value="old('nombre', $trabajo->nombre)" required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Herramientas típicas de este trabajo (opcional)" />
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-2">
                            Al crear una tarea de este tipo, se sugerirán automáticamente (el operario/admin puede
                            ajustar la lista igualmente).
                        </p>
                        @php $herramientasSeleccionadas = collect(old('herramientas', $trabajo->herramientas->pluck('id'))); @endphp
                        @if ($herramientas->isEmpty())
                            <p class="text-sm text-slate-400 dark:text-slate-500">
                                Todavía no has creado ninguna <a href="{{ route('admin.herramientas.create') }}" class="text-brand-700 dark:text-brand-400 hover:underline">herramienta</a>.
                            </p>
                        @else
                            <div class="flex flex-wrap gap-3">
                                @foreach ($herramientas as $herramienta)
                                    <label class="flex items-center gap-1.5 text-sm text-slate-700 dark:text-slate-300">
                                        <input type="checkbox" name="herramientas[]" value="{{ $herramienta->id }}" @checked($herramientasSeleccionadas->contains($herramienta->id))>
                                        {{ $herramienta->nombre }}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        <x-input-error :messages="$errors->get('herramientas')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end pt-2 gap-4">
                        <a href="{{ route('admin.trabajos.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar cambios</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
