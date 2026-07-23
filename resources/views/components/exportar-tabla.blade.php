@props(['ruta'])

{{-- Mantiene los filtros/búsqueda actuales de la página (query string) al exportar. --}}
<div class="flex gap-2">
    <a href="{{ $ruta }}?{{ http_build_query([...request()->query(), 'formato' => 'csv']) }}" class="btn-accent">
        Exportar a Excel
    </a>
    <a href="{{ $ruta }}?{{ http_build_query([...request()->query(), 'formato' => 'pdf']) }}" class="btn-danger">
        Exportar PDF
    </a>
</div>
