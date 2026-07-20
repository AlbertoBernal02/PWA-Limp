@props(['active'])

@php
    $pestañas = [
        'herramientas' => ['label' => 'Herramientas', 'route' => 'admin.herramientas.index'],
        'almacen' => ['label' => 'Mapa del almacén', 'route' => 'admin.almacen.index'],
        'trabajos' => ['label' => 'Trabajos', 'route' => 'admin.trabajos.index'],
    ];
@endphp

<div class="flex gap-4 border-b border-slate-200 dark:border-slate-700 text-sm font-medium">
    @foreach ($pestañas as $clave => $pestaña)
        <a href="{{ route($pestaña['route']) }}"
            class="px-1 py-2 {{ $clave === $active
                ? 'text-brand-700 dark:text-brand-400 border-b-2 border-brand-600'
                : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            {{ $pestaña['label'] }}
        </a>
    @endforeach
</div>
