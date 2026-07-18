@props(['dias'])

@php
    $maxHoras = max(1, collect($dias)->max('horas'));
    $anchoBarra = 28;
    $espacio = 16;
    $alturaMax = 90;
    $anchoTotal = 7 * ($anchoBarra + $espacio);
    $yBase = $alturaMax + 20;

    // Path con esquinas superiores redondeadas y base cuadrada (apoyada en la línea base).
    $rutaBarra = function (float $x, float $yTop, float $ancho, float $alto) {
        $r = min(4, $alto / 2);
        $yBaseBarra = $yTop + $alto;

        return "M{$x}," . ($yTop + $r)
            . " Q{$x},{$yTop} " . ($x + $r) . ",{$yTop}"
            . " L" . ($x + $ancho - $r) . ",{$yTop}"
            . " Q" . ($x + $ancho) . ",{$yTop} " . ($x + $ancho) . "," . ($yTop + $r)
            . " L" . ($x + $ancho) . ",{$yBaseBarra}"
            . " L{$x},{$yBaseBarra}"
            . " Z";
    };
@endphp

<div class="w-full">
    <svg viewBox="0 0 {{ $anchoTotal }} {{ $yBase + 20 }}" class="w-full h-32" role="img" aria-label="Horas trabajadas cada día de la semana">
        <line x1="0" y1="{{ $yBase }}" x2="{{ $anchoTotal }}" y2="{{ $yBase }}" class="stroke-slate-200 dark:stroke-slate-700" stroke-width="1" />

        @foreach ($dias as $i => $dia)
            @php
                $alto = $dia['horas'] > 0 ? max(3, round(($dia['horas'] / $maxHoras) * $alturaMax)) : 0;
                $x = $i * ($anchoBarra + $espacio) + $espacio / 2;
                $yTop = $yBase - $alto;
            @endphp

            @if ($alto > 0)
                <text x="{{ $x + $anchoBarra / 2 }}" y="{{ $yTop - 6 }}" text-anchor="middle" class="fill-slate-500 dark:fill-slate-400" font-size="10">
                    {{ rtrim(rtrim(number_format($dia['horas'], 1), '0'), '.') }}h
                </text>
                <path d="{{ $rutaBarra($x, $yTop, $anchoBarra, $alto) }}" class="{{ $dia['esHoy'] ? 'fill-brand-600 dark:fill-brand-400' : 'fill-brand-500/60 dark:fill-brand-400/60' }}">
                    <title>{{ $dia['etiqueta'] }}: {{ $dia['horas'] }} h</title>
                </path>
            @else
                <text x="{{ $x + $anchoBarra / 2 }}" y="{{ $yBase - 6 }}" text-anchor="middle" class="fill-slate-300 dark:fill-slate-600" font-size="10">—</text>
            @endif

            <text x="{{ $x + $anchoBarra / 2 }}" y="{{ $yBase + 15 }}" text-anchor="middle" class="fill-slate-500 dark:fill-slate-400 {{ $dia['esHoy'] ? 'font-semibold' : '' }}" font-size="10">
                {{ $dia['etiqueta'] }}
            </text>
        @endforeach
    </svg>

    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 text-center">
        {{ collect($dias)->map(fn ($d) => $d['etiqueta'].' '.rtrim(rtrim(number_format($d['horas'], 1), '0'), '.').'h')->implode(' · ') }}
    </p>
</div>
