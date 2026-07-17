<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Editar cliente</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="foto" value="Foto del local/inmueble" />
                        <div class="flex items-center gap-4 mt-1">
                            @if ($cliente->fotoUrl())
                                <img src="{{ $cliente->fotoUrl() }}" alt="{{ $cliente->nombre }}" class="w-14 h-14 rounded-lg object-cover bg-slate-100 dark:bg-slate-700 shrink-0">
                            @else
                                <span class="w-14 h-14 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 shrink-0">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 16.5V4.5A1.5 1.5 0 014.5 3h15A1.5 1.5 0 0121 4.5v15A1.5 1.5 0 0119.5 21h-15A1.5 1.5 0 013 19.5v-3zM9 8.25a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0z" />
                                    </svg>
                                </span>
                            @endif
                            <input id="foto" name="foto" type="file" accept="image/*" class="field-input" />
                        </div>
                        <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="w-full" :value="old('nombre', $cliente->nombre)" required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="direccion" value="Dirección" />
                        <div class="relative">
                            <x-text-input id="direccion" name="direccion" type="text" class="w-full" :value="old('direccion', $cliente->direccion)" autocomplete="off" data-address-autocomplete required />
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Escribe y selecciona una sugerencia; puedes completar piso/puerta después.</p>
                        <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="telefono" value="Teléfono" />
                        <x-text-input id="telefono" name="telefono" type="text" class="w-full" :value="old('telefono', $cliente->telefono)" />
                        <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notas" value="Notas" />
                        <textarea id="notas" name="notas" rows="3" class="field-input">{{ old('notas', $cliente->notas) }}</textarea>
                        <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end pt-2 gap-4">
                        <a href="{{ route('admin.clientes.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar cambios</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
