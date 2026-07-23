<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Clientes</h2>
            <div class="flex gap-2">
                <x-exportar-tabla :ruta="route('admin.clientes.exportar')" />
                <a href="{{ route('admin.clientes.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo cliente
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="card p-4 flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <x-input-label for="q" value="Buscar" />
                    <input type="text" id="q" name="q" value="{{ $busqueda }}" placeholder="Nombre, dirección o teléfono" class="field-input" />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-secondary">Buscar</button>
                    @if ($busqueda)
                        <a href="{{ route('admin.clientes.index') }}" class="btn-secondary">Limpiar</a>
                    @endif
                </div>
            </form>

            @if ($clientes->isEmpty())
                <div class="empty-state">
                    <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>
                    <p class="text-slate-500 dark:text-slate-400">
                        {{ $busqueda ? 'No hay clientes que coincidan con la búsqueda.' : 'Todavía no has dado de alta ningún cliente.' }}
                    </p>
                    <a href="{{ route('admin.clientes.create') }}" class="btn-primary">Crear el primero</a>
                </div>
            @else
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td>
                                        @if ($cliente->fotoUrl())
                                            <img src="{{ $cliente->fotoUrl() }}" alt="{{ $cliente->nombre }}" class="w-9 h-9 rounded-lg object-cover bg-slate-100 dark:bg-slate-700">
                                        @else
                                            <span class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-300 dark:text-slate-600">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 16.5V4.5A1.5 1.5 0 014.5 3h15A1.5 1.5 0 0121 4.5v15A1.5 1.5 0 0119.5 21h-15A1.5 1.5 0 013 19.5v-3zM9 8.25a1.125 1.125 0 11-2.25 0 1.125 1.125 0 012.25 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="font-medium text-slate-800 dark:text-slate-100">
                                        <a href="{{ route('admin.clientes.show', $cliente) }}" class="hover:underline">{{ $cliente->nombre }}</a>
                                    </td>
                                    <td>{{ $cliente->direccion }}</td>
                                    <td>{{ $cliente->telefono ?: '—' }}</td>
                                    <td class="text-right space-x-3 row-actions">
                                        <a href="{{ route('admin.clientes.show', $cliente) }}" class="text-slate-600 dark:text-slate-400 hover:underline">Ver</a>
                                        <a href="{{ route('admin.clientes.edit', $cliente) }}" class="text-brand-700 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('admin.clientes.destroy', $cliente) }}" class="inline" onsubmit="return confirm('¿Eliminar este cliente?');">
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
                    {{ $clientes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
