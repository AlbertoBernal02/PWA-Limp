<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Editar cliente</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="w-full" :value="old('nombre', $cliente->nombre)" required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="direccion" value="Dirección" />
                        <x-text-input id="direccion" name="direccion" type="text" class="w-full" :value="old('direccion', $cliente->direccion)" required />
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
                        <a href="{{ route('admin.clientes.index') }}" class="text-sm text-slate-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar cambios</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
