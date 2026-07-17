<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">{{ $cliente->nombre }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn-secondary">Editar</a>
                <a href="{{ route('admin.clientes.index') }}" class="btn-secondary">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="card p-6 flex flex-wrap gap-6">
                @if ($cliente->fotoUrl())
                    <img src="{{ $cliente->fotoUrl() }}" alt="{{ $cliente->nombre }}" class="w-24 h-24 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-600">
                @endif
                <div class="flex-1 min-w-[200px] space-y-1 text-sm">
                    <p class="text-slate-500 dark:text-slate-400">Dirección</p>
                    <p class="text-slate-800 dark:text-slate-100 font-medium">{{ $cliente->direccion }}</p>
                </div>
                <div class="min-w-[150px] space-y-1 text-sm">
                    <p class="text-slate-500 dark:text-slate-400">Teléfono</p>
                    <p class="text-slate-800 dark:text-slate-100 font-medium">{{ $cliente->telefono ?: '—' }}</p>
                </div>
                @if ($cliente->notas)
                    <div class="w-full space-y-1 text-sm">
                        <p class="text-slate-500 dark:text-slate-400">Notas</p>
                        <p class="text-slate-700 dark:text-slate-300">{{ $cliente->notas }}</p>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="stat-card">
                    <div class="stat-icon bg-brand-50 dark:bg-brand-500/15 text-brand-700 dark:text-brand-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-slate-800 dark:text-slate-100">{{ $total }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Tareas totales</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-slate-800 dark:text-slate-100">{{ $hechas }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Hechas</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-emerald-50 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-semibold text-slate-800 dark:text-slate-100">{{ $cumplimiento }}%</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Cumplimiento</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-3">Historial de tareas</h3>

                @if ($tareas->isEmpty())
                    <div class="empty-state">
                        <p class="text-slate-500 dark:text-slate-400">Este cliente todavía no tiene tareas asignadas.</p>
                    </div>
                @else
                    <div class="table-shell">
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Operario</th>
                                    <th>Estado</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tareas as $tarea)
                                    <tr>
                                        <td>{{ $tarea->fecha->format('d/m/Y') }}</td>
                                        <td>{{ $tarea->hora_estimada ? \Illuminate\Support\Carbon::parse($tarea->hora_estimada)->format('H:i') : '—' }}</td>
                                        <td>{{ $tarea->operario->name }}</td>
                                        <td>
                                            @if ($tarea->estado === 'hecha')
                                                <span class="badge-done">Hecha</span>
                                            @else
                                                <span class="badge-pending">Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="text-slate-500 dark:text-slate-400">{{ $tarea->nota ?: '—' }}</td>
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
    </div>
</x-app-layout>
