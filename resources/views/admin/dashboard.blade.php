<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Panel</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="stat-card">
                    <div class="stat-icon bg-amber-100 text-amber-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800">{{ $pendientesHoy }}</p>
                        <p class="text-sm text-slate-500">Pendientes hoy</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-brand-100 text-brand-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800">{{ $hechasHoy }}</p>
                        <p class="text-sm text-slate-500">Hechas hoy</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-sky-100 text-sky-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 10-11.964 0m11.964 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800">{{ $totalOperarios }}</p>
                        <p class="text-sm text-slate-500">Operarios</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bg-violet-100 text-violet-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800">{{ $totalClientes }}</p>
                        <p class="text-sm text-slate-500">Clientes</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Carga de la semana -->
                <div class="card p-6 lg:col-span-2">
                    <h3 class="font-semibold text-slate-800 mb-4">Tareas de los próximos 7 días</h3>
                    <div class="flex items-end gap-3 h-40">
                        @foreach ($semana as $dia)
                            <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                                <span class="text-xs font-medium text-slate-500">{{ $dia['total'] }}</span>
                                <div class="w-full bg-brand-100 rounded-t-md" style="height: {{ $dia['total'] > 0 ? max(6, ($dia['total'] / $maxSemana) * 100) : 4 }}%">
                                    <div class="w-full h-full rounded-t-md bg-brand-500"></div>
                                </div>
                                <span class="text-xs text-slate-500 capitalize">{{ $dia['fecha']->translatedFormat('D') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Resumen por operario -->
                <div class="card p-6">
                    <h3 class="font-semibold text-slate-800 mb-4">Hoy por operario</h3>
                    <ul class="space-y-4">
                        @forelse ($porOperarioHoy as $fila)
                            <li>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-slate-700">{{ $fila['operario']->name }}</span>
                                    <span class="text-slate-500">{{ $fila['hechas'] }}/{{ $fila['total'] }}</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-brand-500 h-2 rounded-full" style="width: {{ $fila['total'] > 0 ? ($fila['hechas'] / $fila['total']) * 100 : 0 }}%"></div>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">Nadie tiene tareas asignadas hoy.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Tareas de hoy -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-slate-800">Tareas de hoy</h3>
                    <a href="{{ route('admin.tareas.index') }}" class="text-sm text-brand-700 hover:underline">Ver todas →</a>
                </div>

                @if ($tareasHoy->isEmpty())
                    <div class="empty-state">
                        <p class="text-slate-500">No hay tareas programadas para hoy.</p>
                        <a href="{{ route('admin.tareas.create') }}" class="btn-primary">Crear tarea</a>
                    </div>
                @else
                    <div class="table-shell">
                        <table>
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Cliente</th>
                                    <th>Operario</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tareasHoy as $tarea)
                                    <tr>
                                        <td>{{ $tarea->hora_estimada ? \Illuminate\Support\Carbon::parse($tarea->hora_estimada)->format('H:i') : '—' }}</td>
                                        <td>{{ $tarea->cliente->nombre }}</td>
                                        <td>{{ $tarea->operario->name }}</td>
                                        <td>
                                            @if ($tarea->estado === 'hecha')
                                                <span class="badge-done">Hecha</span>
                                            @else
                                                <span class="badge-pending">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
