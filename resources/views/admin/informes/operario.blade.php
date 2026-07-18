<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe {{ $operario->name }}</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitulo { color: #64748b; margin-bottom: 18px; }
        .resumen { margin-bottom: 18px; }
        .resumen strong { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #f1f5f9; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #64748b; }
        td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        .sin-horas { color: #cbd5e1; }
    </style>
</head>
<body>
    <h1>{{ $operario->name }}</h1>
    <p class="subtitulo">{{ $operario->email }} @if($operario->telefono) · {{ $operario->telefono }} @endif</p>
    <p class="subtitulo">Informe de horas de {{ $inicio->translatedFormat('F Y') }}</p>

    <div class="resumen">
        <strong>{{ number_format($totalHoras, 2) }} horas</strong> trabajadas en el mes
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Día</th>
                <th>Horas trabajadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dias as $dia)
                <tr>
                    <td>{{ $dia['fecha']->format('d/m/Y') }}</td>
                    <td>{{ $dia['fecha']->translatedFormat('l') }}</td>
                    <td class="{{ $dia['horas'] > 0 ? '' : 'sin-horas' }}">
                        {{ $dia['horas'] > 0 ? number_format($dia['horas'], 2) : '—' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
