<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Nueva tarea</h2>
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
                        <x-input-label value="Operario(s)" />
                        <div class="flex flex-wrap gap-3 mt-1">
                            @foreach ($operarios as $operario)
                                <label class="flex items-center gap-2 px-3 py-2 rounded-lg ring-1 ring-inset ring-slate-300 dark:ring-slate-600 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="operario_ids[]"
                                        value="{{ $operario->id }}"
                                        class="rounded border-slate-300 dark:border-slate-600 text-brand-600 focus:ring-brand-500"
                                        @checked(in_array($operario->id, old('operario_ids', [])))
                                    >
                                    {{ $operario->name }}
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Selecciona varios para crear la misma tarea a cada uno.</p>
                        <x-input-error :messages="$errors->get('operario_ids')" class="mt-2" />
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
                        <x-input-label for="repetir_hasta" value="Repetir cada semana hasta (opcional)" />
                        <x-text-input id="repetir_hasta" name="repetir_hasta" type="date" class="w-full" :value="old('repetir_hasta')" />
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Crea una tarea igual cada semana, el mismo día, hasta esta fecha (incluida).</p>
                        <x-input-error :messages="$errors->get('repetir_hasta')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción" />
                        <textarea id="descripcion" name="descripcion" rows="3" class="field-input">{{ old('descripcion') }}</textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                    </div>

                    @if ($herramientas->isNotEmpty())
                        <div>
                            <x-input-label value="Herramientas a llevar (opcional)" />
                            <div class="flex flex-wrap gap-3 mt-1">
                                @foreach ($herramientas as $herramienta)
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg ring-1 ring-inset ring-slate-300 dark:ring-slate-600 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="herramienta_ids[]"
                                            value="{{ $herramienta->id }}"
                                            class="rounded border-slate-300 dark:border-slate-600 text-brand-600 focus:ring-brand-500"
                                            @checked(in_array($herramienta->id, old('herramienta_ids', [])))
                                        >
                                        {{ $herramienta->nombre }}
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">El operario las verá en la tarea antes de empezar.</p>
                            <x-input-error :messages="$errors->get('herramienta_ids')" class="mt-2" />
                        </div>
                    @endif

                    <div class="flex items-center justify-end pt-2 gap-4">
                        <a href="{{ route('admin.tareas.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:underline">Cancelar</a>
                        <x-primary-button>Crear tarea</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
