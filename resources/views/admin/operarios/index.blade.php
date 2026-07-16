<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">Operarios</h2>
            <a href="{{ route('admin.operarios.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo operario
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if ($operarios->isEmpty())
                <div class="empty-state">
                    <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 10-11.964 0m11.964 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-slate-500">Todavía no has dado de alta ningún operario.</p>
                    <a href="{{ route('admin.operarios.create') }}" class="btn-primary">Crear el primero</a>
                </div>
            @else
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Correo electrónico</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($operarios as $operario)
                                <tr>
                                    <td class="font-medium text-slate-800">{{ $operario->name }}</td>
                                    <td>{{ $operario->email }}</td>
                                    <td class="text-right space-x-3">
                                        <a href="{{ route('admin.operarios.edit', $operario) }}" class="text-brand-700 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('admin.operarios.destroy', $operario) }}" class="inline" onsubmit="return confirm('¿Eliminar este operario?');">
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
