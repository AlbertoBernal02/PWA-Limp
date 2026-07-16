<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Nuevo cliente</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('admin.clientes.store') }}" class="space-y-4" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="foto" value="Foto del local/inmueble" />
                        <input id="foto" name="foto" type="file" accept="image/*" class="field-input" />
                        <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="w-full" :value="old('nombre')" required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="direccion" value="Dirección" />
                        <x-text-input id="direccion" name="direccion" type="text" class="w-full" :value="old('direccion')" required />
                        <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="telefono" value="Teléfono" />
                        <x-text-input id="telefono" name="telefono" type="text" class="w-full" :value="old('telefono')" />
                        <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notas" value="Notas" />
                        <textarea id="notas" name="notas" rows="3" class="field-input">{{ old('notas') }}</textarea>
                        <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end pt-2 gap-4">
                        <a href="{{ route('admin.clientes.index') }}" class="text-sm text-slate-600 hover:underline">Cancelar</a>
                        <x-primary-button>Crear cliente</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
