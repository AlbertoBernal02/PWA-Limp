<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Configuración — Herramientas</h2>
            <a href="{{ route('admin.herramientas.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva herramienta
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex gap-4 border-b border-slate-200 dark:border-slate-700 text-sm font-medium">
                <a href="{{ route('admin.herramientas.index') }}" class="px-1 py-2 text-brand-700 dark:text-brand-400 border-b-2 border-brand-600">
                    Herramientas
                </a>
                <a href="{{ route('admin.almacen.index') }}" class="px-1 py-2 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                    Mapa del almacén
                </a>
            </div>

            <p class="text-sm text-slate-500 dark:text-slate-400">
                Gestiona el catálogo de herramientas/material que los operarios pueden necesitar llevar a
                una tarea, y la cantidad que hay en almacén de cada una (actualízala cuando se compre más
                o se gaste/rompa alguna).
            </p>

            @if ($herramientas->isEmpty())
                <div class="empty-state">
                    <p class="text-slate-500 dark:text-slate-400">Todavía no has dado de alta ninguna herramienta.</p>
                    <a href="{{ route('admin.herramientas.create') }}" class="btn-primary">Crear la primera</a>
                </div>
            @else
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Cantidad en almacén</th>
                                <th>Ubicación</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($herramientas as $herramienta)
                                <tr>
                                    <td class="font-medium text-slate-800 dark:text-slate-100">{{ $herramienta->nombre }}</td>
                                    <td>
                                        @if ($herramienta->cantidad <= 0)
                                            <span class="badge bg-red-100 dark:bg-red-500/15 text-red-700 dark:text-red-400">Sin stock</span>
                                        @else
                                            {{ $herramienta->cantidad }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($herramienta->hueco)
                                            <span class="badge bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $herramienta->hueco->codigo() }}</span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right space-x-3 row-actions">
                                        <a href="{{ route('admin.herramientas.edit', $herramienta) }}" class="text-brand-700 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('admin.herramientas.destroy', $herramienta) }}" class="inline" onsubmit="return confirm('¿Eliminar esta herramienta? Se quitará también de las tareas donde estuviera asignada.');">
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
            @endif
        </div>
    </div>
</x-app-layout>
