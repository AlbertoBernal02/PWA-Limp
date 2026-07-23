<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Fichajes</h2>
            <x-exportar-tabla :ruta="route('admin.fichajes.exportar')" />
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($resumen->isEmpty())
                <div class="empty-state">
                    <p class="text-slate-500 dark:text-slate-400">Todavía no hay operarios dados de alta.</p>
                </div>
            @else
                <div class="table-shell">
                    <table>
                        <thead>
                            <tr>
                                <th>Operario</th>
                                <th>Estado ahora</th>
                                <th>Hoy</th>
                                <th>Esta semana</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resumen as $fila)
                                <tr>
                                    <td class="font-medium text-slate-800 dark:text-slate-100">{{ $fila['operario']->name }}</td>
                                    <td>
                                        @if ($fila['ultimoTipo'] === 'inicio_descanso')
                                            <span class="badge-pending">En descanso</span>
                                        @elseif ($fila['ultimoTipo'] && $fila['ultimoTipo'] !== 'salida')
                                            <span class="badge-done">Fichado</span>
                                        @else
                                            <span class="badge bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">Fuera</span>
                                        @endif
                                    </td>
                                    <td>{{ \App\Support\Jornada::formatoHoras($fila['segundosHoy']) }}</td>
                                    <td>{{ \App\Support\Jornada::formatoHoras($fila['segundosSemana']) }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.fichajes.show', $fila['operario']) }}" class="text-brand-700 hover:underline">Ver detalle</a>
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
