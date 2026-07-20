<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Configuración — Trabajos</h2>
            <a href="{{ route('admin.trabajos.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo trabajo
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-configuracion-tabs active="trabajos" />

            <p class="text-sm text-slate-500 dark:text-slate-400">
                Tipos de trabajo (limpieza de oficinas, síndrome de Diógenes, placas solares...) para organizar el
                almacén por servicio y sugerir automáticamente qué herramientas suele llevar cada tipo al crear una tarea.
            </p>

            @if ($trabajos->isEmpty())
                <div class="empty-state">
                    <p class="text-slate-500 dark:text-slate-400">Todavía no has dado de alta ningún tipo de trabajo.</p>
                    <a href="{{ route('admin.trabajos.create') }}" class="btn-primary">Crear el primero</a>
                </div>
            @else
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Herramientas típicas</th>
                                <th>Tareas</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trabajos as $trabajo)
                                <tr>
                                    <td class="font-medium text-slate-800 dark:text-slate-100">{{ $trabajo->nombre }}</td>
                                    <td>{{ $trabajo->herramientas_count }}</td>
                                    <td>{{ $trabajo->tareas_count }}</td>
                                    <td class="text-right space-x-3 row-actions">
                                        <a href="{{ route('admin.trabajos.edit', $trabajo) }}" class="text-brand-700 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('admin.trabajos.destroy', $trabajo) }}" class="inline" onsubmit="return confirm('¿Eliminar este trabajo? Las tareas que lo tuvieran asignado se quedarán sin tipo de trabajo.');">
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
