<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .subtitulo { color: #64748b; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #f1f5f9; padding: 6px 8px; font-size: 10px; text-transform: uppercase; color: #64748b; }
        td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <p class="subtitulo">{{ now()->translatedFormat('d \d\e F \d\e Y, H:i') }} · {{ count($filas) }} {{ count($filas) === 1 ? 'fila' : 'filas' }}</p>

    <table>
        <thead>
            <tr>
                @foreach ($cabeceras as $cabecera)
                    <th>{{ $cabecera }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($filas as $fila)
                <tr>
                    @foreach ($fila as $valor)
                        <td>{{ $valor === null || $valor === '' ? '—' : $valor }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($cabeceras) }}">Sin datos.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
