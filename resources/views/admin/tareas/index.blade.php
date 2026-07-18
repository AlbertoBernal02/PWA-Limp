<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Tareas</h2>
            <a href="{{ route('admin.tareas.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva tarea
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <!-- Filtros -->
            <form method="GET" class="card p-4 flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <x-input-label for="q" value="Buscar" />
                    <input type="text" id="q" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Cliente, operario o descripción" class="field-input" />
                </div>

                <div>
                    <x-input-label for="fecha" value="Fecha" />
                    <input type="date" id="fecha" name="fecha" value="{{ $filtros['fecha'] ?? '' }}" class="field-input" />
                </div>

                <div>
                    <x-input-label for="operario_id" value="Operario" />
                    <select id="operario_id" name="operario_id" class="field-input">
                        <option value="">Todos</option>
                        @foreach ($operarios as $operario)
                            <option value="{{ $operario->id }}" @selected(($filtros['operario_id'] ?? null) == $operario->id)>{{ $operario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="estado" value="Estado" />
                    <select id="estado" name="estado" class="field-input">
                        <option value="">Todos</option>
                        <option value="pendiente" @selected(($filtros['estado'] ?? null) === 'pendiente')>Pendiente</option>
                        <option value="hecha" @selected(($filtros['estado'] ?? null) === 'hecha')>Hecha</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn-secondary">Filtrar</button>
                    @if (array_filter($filtros))
                        <a href="{{ route('admin.tareas.index') }}" class="btn-secondary">Limpiar</a>
                    @endif
                </div>

                @if ($duracionMedia)
                    <div class="ms-auto text-sm text-slate-500 dark:text-slate-400">
                        Duración media {{ array_filter($filtros) ? 'de este filtro' : '' }}: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $duracionMedia }}</span>
                    </div>
                @endif
            </form>

            @if ($tareas->isEmpty())
                <div class="empty-state">
                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-slate-500 dark:text-slate-400">No hay tareas que coincidan con la búsqueda.</p>
                    <a href="{{ route('admin.tareas.create') }}" class="btn-primary">Crear tarea</a>
                </div>
            @else
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Operario</th>
                                <th>Estado</th>
                                <th>Duración</th>
                                <th>Evidencia</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tareas as $tarea)
                                <tr>
                                    <td>{{ $tarea->fecha->format('d/m/Y') }}</td>
                                    <td>{{ $tarea->hora_estimada ? \Illuminate\Support\Carbon::parse($tarea->hora_estimada)->format('H:i') : '—' }}</td>
                                    <td class="font-medium text-slate-800 dark:text-slate-100">{{ $tarea->cliente->nombre }}</td>
                                    <td>{{ $tarea->operario->name }}</td>
                                    <td>
                                        @if ($tarea->estado === 'hecha')
                                            <span class="badge-done">Hecha</span>
                                        @else
                                            <span class="badge-pending">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>{{ $tarea->duracionFormateada() ?? '—' }}</td>
                                    <td>
                                        @if ($tarea->fotoEvidenciaUrl())
                                            <a href="{{ $tarea->fotoEvidenciaUrl() }}" target="_blank">
                                                <img src="{{ $tarea->fotoEvidenciaUrl() }}" alt="Evidencia" class="w-9 h-9 rounded-lg object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                                            </a>
                                        @else
                                            <span class="text-slate-300 dark:text-slate-600">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right space-x-3 row-actions">
                                        <a href="{{ route('admin.tareas.edit', $tarea) }}" class="text-brand-700 hover:underline">Editar</a>
                                        <a href="{{ route('admin.tareas.duplicar', $tarea) }}" class="text-slate-600 dark:text-slate-400 hover:underline">Duplicar</a>
                                        <form method="POST" action="{{ route('admin.tareas.destroy', $tarea) }}" class="inline" onsubmit="return confirm('¿Eliminar esta tarea?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $tareas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
