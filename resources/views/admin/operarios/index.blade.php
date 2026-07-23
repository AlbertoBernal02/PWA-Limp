<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Operarios</h2>
            <div class="flex gap-2">
                <x-exportar-tabla :ruta="route('admin.operarios.exportar')" />
                <a href="{{ route('admin.operarios.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo operario
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="card p-4 flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <x-input-label for="q" value="Buscar" />
                    <input type="text" id="q" name="q" value="{{ $busqueda }}" placeholder="Nombre, email o teléfono" class="field-input" />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-secondary">Buscar</button>
                    @if ($busqueda)
                        <a href="{{ route('admin.operarios.index') }}" class="btn-secondary">Limpiar</a>
                    @endif
                </div>
            </form>

            @if ($operarios->isEmpty())
                <div class="empty-state">
                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 10-11.964 0m11.964 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-slate-500 dark:text-slate-400">
                        {{ $busqueda ? 'No hay operarios que coincidan con la búsqueda.' : 'Todavía no has dado de alta ningún operario.' }}
                    </p>
                    <a href="{{ route('admin.operarios.create') }}" class="btn-primary">Crear el primero</a>
                </div>
            @else
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nombre</th>
                                <th>Correo electrónico</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operarios as $operario)
                                <tr class="{{ ! $operario->activo ? 'opacity-60' : '' }}">
                                    <td>
                                        <x-avatar :src="$operario->avatarUrl()" :iniciales="$operario->iniciales()" class="w-9 h-9 text-xs" />
                                    </td>
                                    <td class="font-medium text-slate-800 dark:text-slate-100">{{ $operario->name }}</td>
                                    <td>{{ $operario->email }}</td>
                                    <td>{{ $operario->telefono ?? '—' }}</td>
                                    <td>
                                        @if ($operario->activo)
                                            <span class="badge-done">Activo</span>
                                        @else
                                            <span class="badge bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-right space-x-3 row-actions">
                                        <a href="{{ route('admin.operarios.edit', $operario) }}" class="text-brand-700 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('admin.operarios.toggle-activo', $operario) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-amber-600 hover:underline">{{ $operario->activo ? 'Desactivar' : 'Activar' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.operarios.destroy', $operario) }}" class="inline" onsubmit="return confirm('¿Eliminar este operario? Se perderá su historial de tareas asociado.');">
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
                    {{ $operarios->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
