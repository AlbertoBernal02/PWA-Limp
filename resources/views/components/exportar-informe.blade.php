@props(['accionBase', 'mesPorDefecto'])

<div x-data="{ mes: '{{ $mesPorDefecto }}' }" class="flex flex-wrap items-end gap-3">
    <div>
        <x-input-label for="mes-informe" value="Mes del informe" />
        <input type="month" id="mes-informe" x-model="mes" class="field-input" required>
    </div>
    <button type="button" class="btn-secondary" @click="location.href = '{{ $accionBase }}?formato=pdf&mes=' + mes">
        Descargar PDF
    </button>
    <button type="button" class="btn-secondary" @click="location.href = '{{ $accionBase }}?formato=csv&mes=' + mes">
        Descargar Excel (CSV)
    </button>
</div>
