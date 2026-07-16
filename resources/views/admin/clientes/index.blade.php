<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Clientes</h2>
            <a href="{{ route('admin.clientes.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo cliente
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if ($clientes->isEmpty())
                <div class="empty-state">
                    <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>
                    <p class="text-slate-500">Todavía no has dado de alta ningún cliente.</p>
                    <a href="{{ route('admin.clientes.create') }}" class="btn-primary">Crear el primero</a>
                </div>
            @else
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Teléfono</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td class="font-medium text-slate-800">{{ $cliente->nombre }}</td>
                                    <td>{{ $cliente->direccion }}</td>
                                    <td>{{ $cliente->telefono ?: '—' }}</td>
                                    <td class="text-right space-x-3">
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
