<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Nueva tarea</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('admin.tareas.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="cliente_id" value="Cliente" />
                        <select id="cliente_id" name="cliente_id" class="field-input" required>
                            <option value="">Selecciona un cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="operario_id" value="Operario" />
                        <select id="operario_id" name="operario_id" class="field-input" required>
                            <option value="">Selecciona un operario</option>
                            @foreach ($operarios as $operario)
                                <option value="{{ $operario->id }}" @selected(old('operario_id') == $operario->id)>{{ $operario->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('operario_id')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="fecha" value="Fecha" />
                            <x-text-input id="fecha" name="fecha" type="date" class="w-full" :value="old('fecha', now()->toDateString())" required />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="hora_estimada" value="Hora (opcional)" />
                            <x-text-input id="hora_estimada" name="hora_estimada" type="time" class="w-full" :value="old('hora_estimada')" />
                            <x-input-error :messages="$errors->get('hora_estimada')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción" />
                        <textarea id="descripcion" name="descripcion" rows="3" class="field-input">{{ old('descripcion') }}</textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end pt-2 gap-4">
                        <a href="{{ route('admin.tareas.index') }}" class="text-sm text-slate-600 hover:underline">Cancelar</a>
                        <x-primary-button>Crear tarea</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
