<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe {{ $cliente->nombre }}</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitulo { color: #64748b; margin-bottom: 18px; }
        .resumen { margin-bottom: 18px; }
        .resumen span { display: inline-block; margin-right: 24px; }
        .resumen strong { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #f1f5f9; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #64748b; }
        td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        .badge-hecha { color: #147063; font-weight: bold; }
        .badge-pendiente { color: #b45309; }
    </style>
</head>
<body>
    <h1>{{ $cliente->nombre }}</h1>
    <p class="subtitulo">{{ $cliente->direccion }} @if($cliente->telefono) · {{ $cliente->telefono }} @endif</p>
    <p class="subtitulo">Informe de {{ $inicio->translatedFormat('F Y') }}</p>

    <div class="resumen">
        <span><strong>{{ $totales['total'] }}</strong> tareas totales</span>
        <span><strong>{{ $totales['hechas'] }}</strong> completadas</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Operario</th>
                <th>Estado</th>
                <th>Duración</th>
                <th>Nota</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tareas as $tarea)
                <tr>
                    <td>{{ $tarea->fecha->format('d/m/Y') }}</td>
                    <td>{{ $tarea->hora_estimada ? \Illuminate\Support\Carbon::parse($tarea->hora_estimada)->format('H:i') : '—' }}</td>
                    <td>{{ $tarea->operario->name }}</td>
                    <td class="{{ $tarea->estado === 'hecha' ? 'badge-hecha' : 'badge-pendiente' }}">
                        {{ $tarea->estado === 'hecha' ? 'Hecha' : 'Pendiente' }}
                    </td>
                    <td>{{ $tarea->duracionFormateada() ?? '—' }}</td>
                    <td>{{ $tarea->nota ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Sin tareas en este periodo.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
